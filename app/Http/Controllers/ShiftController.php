<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\CashierShift;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ShiftController extends Controller
{
    /**
     * Menampilkan daftar definisi shift.
     */
    public function index()
    {
        $shifts = Shift::all();
        return view('shifts.index', compact('shifts'));
    }

    /**
     * Menampilkan form untuk membuat shift baru.
     */
    public function create()
    {
        return view('shifts.create');
    }

    /**
     * Menyimpan definisi shift baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:shifts,name',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        Shift::create($request->all());

        return redirect()->route('shifts.index')->with('success', 'Shift berhasil ditambahkan.');
    }

    /**
     * Memulai shift untuk kasir yang sedang login.
     */
    public function startShift(Request $request)
    {
        $request->validate([
            'shift_id' => 'required|exists:shifts,id',
            'initial_cash' => 'required|numeric|min:0',
        ]);

        $user = Auth::user();
        $selectedShift = Shift::findOrFail($request->shift_id);
        $now = Carbon::now();

        // --- Validasi 1: Cek Shift Aktif yang Belum Ditutup Secara Fisik ---
        $activeShift = CashierShift::where('user_id', $user->id)
                                ->where('status', 'open')
                                ->first();

        if ($activeShift) {
            // Jika ada shift aktif, periksa apakah secara logis sudah berakhir
            $activeShiftDefinition = $activeShift->shift;
            $activeShiftEndTime = Carbon::parse($activeShiftDefinition->end_time);

            // Jika shift aktif melewati tengah malam, sesuaikan tanggalnya
            if ($activeShiftEndTime->lt(Carbon::parse($activeShiftDefinition->start_time))) {
                // Jika shift aktif dimulai kemarin dan berakhir hari ini
                if ($activeShift->start_time->isYesterday()) {
                    $activeShiftEndTime->addDay($activeShift->start_time->diffInDays($now));
                } else { // Jika shift aktif dimulai hari ini dan berakhir besok
                    $activeShiftEndTime->addDay();
                }
            }
            // Pastikan activeShiftEndTime berada di hari yang sama dengan $activeShift->start_time
            $activeShiftEndTime = $activeShift->start_time->copy()->setTimeFromTimeString($activeShiftDefinition->end_time);
            if ($activeShiftEndTime->lt($activeShift->start_time)) {
                $activeShiftEndTime->addDay();
            }


            if ($now->lt($activeShiftEndTime)) {
                // Shift aktif secara fisik dan logis masih berjalan
                return redirect()->route('pos.index')->with('error', 'Anda sudah memiliki shift aktif ' . $activeShiftDefinition->name . ' yang dimulai pada ' . $activeShift->start_time->format('d-m-Y H:i') . '. Harap selesaikan shift tersebut terlebih dahulu.');
            } else {
                // Shift aktif secara fisik (status 'open') tetapi secara logis sudah berakhir
                // Ini adalah skenario di mana kasir lupa menutup shift.
                // Kita bisa secara otomatis menutup shift ini atau meminta kasir menutupnya.
                // Untuk saat ini, kita akan meminta kasir menutupnya secara manual untuk audit.
                // Opsi lain: $activeShift->update(['end_time' => $activeShiftEndTime, 'status' => 'closed']);
                return redirect()->route('pos.index')->with('warning', 'Shift Anda sebelumnya (' . $activeShiftDefinition->name . ') sudah melewati waktu berakhir pada ' . $activeShiftEndTime->format('H:i') . '. Mohon akhiri shift tersebut untuk melanjutkan.');
            }
        }

        // --- Validasi 2: Pengecekan Waktu Saat Ini Terhadap Definisi Shift yang Dipilih ---
        $shiftStartTime = Carbon::parse($selectedShift->start_time);
        $shiftEndTime = Carbon::parse($selectedShift->end_time);

        // Sesuaikan end_time jika shift melewati tengah malam
        if ($shiftEndTime->lt($shiftStartTime)) {
            $shiftEndTime->addDay();
        }

        // Buat objek Carbon untuk waktu mulai dan berakhir shift pada hari ini
        $currentDayShiftStart = $now->copy()->setTimeFromTimeString($selectedShift->start_time);
        $currentDayShiftEnd = $now->copy()->setTimeFromTimeString($selectedShift->end_time);

        // Jika shift melewati tengah malam, kita perlu memeriksa dua rentang waktu
        $isWithinShift = false;
        if ($selectedShift->end_time < $selectedShift->start_time) { // Shift melewati tengah malam (e.g., 20:00 - 04:00)
            // Cek apakah sekarang antara start_time hari ini sampai 23:59:59
            // ATAU antara 00:00:00 sampai end_time hari berikutnya
            if ($now->between($currentDayShiftStart, $now->copy()->endOfDay()) ||
                $now->between($now->copy()->startOfDay(), $currentDayShiftEnd)) {
                $isWithinShift = true;
            }
        } else { // Shift dalam satu hari (e.g., 08:00 - 16:00)
            if ($now->between($currentDayShiftStart, $currentDayShiftEnd)) {
                $isWithinShift = true;
            }
        }

        if (!$isWithinShift) {
            return back()->with('error', 'Anda tidak dapat memulai shift ' . $selectedShift->name . ' pada waktu ini. Shift ini berlaku dari ' . $selectedShift->start_time . ' hingga ' . $selectedShift->end_time . '.');
        }

        // --- Validasi 3: Tidak Izinkan User Mengambil Dua Shift Aktif atau Shift Bersamaan Pada Hari yang Sama ---
        // Ini sudah tercakup sebagian oleh Validasi 1, tetapi kita bisa lebih spesifik untuk mencegah overlap di masa lalu/depan
        $existingShiftsToday = CashierShift::where('user_id', $user->id)
                                        ->whereDate('start_time', $now->toDateString())
                                        ->where('status', 'closed') // Hanya cek shift yang sudah ditutup
                                        ->get();

        foreach ($existingShiftsToday as $shift) {
            $shiftDef = $shift->shift;
            $shiftStart = $shift->start_time;
            $shiftEnd = $shift->end_time;

            // Jika shift yang akan dimulai tumpang tindih dengan shift yang sudah selesai hari ini
            // Ini adalah skenario yang lebih kompleks, biasanya tidak terjadi jika shift didefinisikan dengan baik
            // Untuk kesederhanaan, kita asumsikan shift yang sudah closed tidak akan tumpang tindih dengan shift baru yang valid.
            // Jika diperlukan validasi overlap yang ketat, logika ini perlu diperluas.
        }


        // Mulai shift baru
        CashierShift::create([
            'user_id' => $user->id,
            'shift_id' => $request->shift_id,
            'start_time' => $now,
            'initial_cash' => $request->initial_cash,
            'status' => 'open',
        ]);

        return redirect()->route('pos.index')->with('success', 'Shift berhasil dimulai!');
    }

    /**
     * Mengakhiri shift untuk kasir yang sedang login.
     */
     public function endShift(Request $request)
    {
        $activeShift = CashierShift::where('user_id', Auth::id())
                                      ->where('status', 'open')
                                      ->first();

        if (!$activeShift) {
            return back()->with('error', 'Anda tidak memiliki shift yang sedang berjalan.');
        }

        DB::transaction(function () use ($activeShift) {
            // 1. Hitung total penjualan selama shift ini.
            $totalSales = Penjualan::where('cashier_shift_id', $activeShift->id)->sum('total');

            // 2. Hitung modal akhir di server agar akurat.
            $finalCash = $activeShift->initial_cash + $totalSales;

            // 3. Simpan semua data yang sudah terverifikasi.
            $activeShift->update([
                'end_time' => Carbon::now(),
                'final_cash' => $finalCash,       // Gunakan hasil perhitungan server
                'total_sales' => $totalSales,     // Simpan juga total penjualan
                'status' => 'closed',
            ]);
        });

        // Logout otomatis untuk keamanan
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/auth.pilih-login')->with('success', 'Shift berhasil diakhiri. Sampai jumpa!');
    }

    /**
     * Menampilkan ringkasan shift kasir.
     */
    public function summary(Request $request)
    {
        $query = CashierShift::with(['user', 'shift']);

        // Filter berdasarkan tanggal
        if ($request->filled('date')) {
            $query->whereDate('start_time', $request->date);
        }
        // Filter berdasarkan kasir (hanya admin yang bisa melihat semua kasir)
        if (Auth::user()->role === 'admin' && $request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        } elseif (Auth::user()->role === 'kasir') {
            $query->where('user_id', Auth::id()); // Kasir hanya bisa melihat shiftnya sendiri
        }

        $cashierShifts = $query->latest('start_time')->paginate(10);
        $users = Auth::user()->role === 'admin' ? \App\Models\User::where('role', 'kasir')->get() : collect(); // Untuk filter di admin

        return view('shifts.summary', compact('cashierShifts', 'users'));
    }
}