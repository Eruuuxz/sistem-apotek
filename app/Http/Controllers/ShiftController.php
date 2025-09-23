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

        // Cek apakah ada shift aktif untuk user ini
        $activeShift = CashierShift::where('user_id', Auth::id())
                                   ->whereNull('end_time')
                                   ->first();

        if ($activeShift) {
            return redirect()->route('pos.index')->with('error', 'Anda sudah memiliki shift aktif.');
        }

        // --- START PERUBAHAN ---
        $selectedShift = Shift::findOrFail($request->shift_id);
        $now = Carbon::now();
        $shiftStartTime = Carbon::parse($selectedShift->start_time);
        $shiftEndTime = Carbon::parse($selectedShift->end_time);

        // Jika shift berakhir di hari berikutnya (misal 20:00 - 04:00), sesuaikan end_time
        if ($shiftEndTime->lt($shiftStartTime)) {
            $shiftEndTime->addDay();
        }

        // Buat objek Carbon untuk waktu mulai dan berakhir shift pada hari ini
        $currentDayShiftStart = Carbon::createFromTime($shiftStartTime->hour, $shiftStartTime->minute, 0);
        $currentDayShiftEnd = Carbon::createFromTime($shiftEndTime->hour, $shiftEndTime->minute, 0);

        // Jika shift melewati tengah malam, kita perlu memeriksa dua rentang waktu
        // Contoh: Shift Sore 16:00 - 20:00 (hari yang sama)
        // Contoh: Shift Malam 20:00 - 04:00 (melewati tengah malam)
        $isWithinShift = false;
        if ($shiftEndTime->lt($shiftStartTime)) { // Shift melewati tengah malam
            // Cek apakah sekarang antara start_time hari ini sampai 23:59:59
            // ATAU antara 00:00:00 sampai end_time hari berikutnya
            if ($now->between($currentDayShiftStart, Carbon::createFromTime(23, 59, 59)) ||
                $now->between(Carbon::createFromTime(0, 0, 0), $currentDayShiftEnd)) {
                $isWithinShift = true;
            }
        } else { // Shift dalam satu hari
            if ($now->between($currentDayShiftStart, $currentDayShiftEnd)) {
                $isWithinShift = true;
            }
        }
        
        if (!$isWithinShift) {
            return back()->with('error', 'Anda tidak dapat memulai shift ' . $selectedShift->name . ' pada waktu ini. Shift ini berlaku dari ' . $selectedShift->start_time . ' hingga ' . $selectedShift->end_time . '.');
        }
        // --- END PERUBAHAN ---

        // Mulai shift baru
        CashierShift::create([
            'user_id' => Auth::id(),
            'shift_id' => $request->shift_id,
            'start_time' => Carbon::now(),
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