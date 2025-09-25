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

    // Validasi 1: Cek shift aktif
    $activeShift = CashierShift::where('user_id', $user->id)
        ->where('status', 'open')
        ->first();

    if ($activeShift) {
        $activeShiftDefinition = $activeShift->shift;
        
        // Hitung end time yang benar untuk shift aktif
        $activeShiftEndTime = $activeShift->start_time->copy()
            ->setTimeFromTimeString($activeShiftDefinition->end_time);
            
        // Jika shift melewati tengah malam
        if ($activeShiftDefinition->end_time < $activeShiftDefinition->start_time) {
            $activeShiftEndTime->addDay();
        }

        if ($now->lt($activeShiftEndTime)) {
            return redirect()->route('pos.index')->with('error', 
                'Anda sudah memiliki shift aktif ' . $activeShiftDefinition->name . 
                ' yang dimulai pada ' . $activeShift->start_time->format('d-m-Y H:i') . 
                '. Harap selesaikan shift tersebut terlebih dahulu.');
        } else {
            return redirect()->route('pos.index')->with('warning', 
                'Shift Anda sebelumnya (' . $activeShiftDefinition->name . 
                ') sudah melewati waktu berakhir. Mohon akhiri shift tersebut untuk melanjutkan.');
        }
    }

    // Validasi 2: Cek waktu shift yang dipilih
    $currentTime = $now->format('H:i');
    $shiftStart = $selectedShift->start_time;
    $shiftEnd = $selectedShift->end_time;
    
    $isWithinShift = false;
    
    if ($shiftEnd < $shiftStart) {
        // Shift melewati tengah malam
        $isWithinShift = ($currentTime >= $shiftStart) || ($currentTime <= $shiftEnd);
    } else {
        // Shift normal dalam satu hari
        $isWithinShift = ($currentTime >= $shiftStart) && ($currentTime <= $shiftEnd);
    }

    if (!$isWithinShift) {
        return back()->with('error', 
            'Anda tidak dapat memulai shift ' . $selectedShift->name . 
            ' pada waktu ini. Shift ini berlaku dari ' . $shiftStart . 
            ' hingga ' . $shiftEnd . '.');
    }

    // Validasi 3: Cek overlap dengan shift yang sudah closed hari ini
    $todayShifts = CashierShift::where('user_id', $user->id)
        ->whereDate('start_time', $now->toDateString())
        ->where('status', 'closed')
        ->get();

    foreach ($todayShifts as $existingShift) {
        $existingShiftDef = $existingShift->shift;
        
        // Cek apakah ada overlap waktu
        if ($this->hasTimeOverlap($selectedShift, $existingShiftDef)) {
            return back()->with('error', 
                'Shift ' . $selectedShift->name . 
                ' bertabrakan dengan shift ' . $existingShiftDef->name . 
                ' yang sudah Anda selesaikan hari ini.');
        }
    }

    // Buat shift baru
    CashierShift::create([
        'user_id' => $user->id,
        'shift_id' => $request->shift_id,
        'start_time' => $now,
        'initial_cash' => $request->initial_cash,
        'status' => 'open',
    ]);

    return redirect()->route('pos.index')->with('success', 'Shift berhasil dimulai!');
}

private function hasTimeOverlap($shift1, $shift2)
{
    // Implementasi logic untuk cek overlap waktu shift
    $start1 = $shift1->start_time;
    $end1 = $shift1->end_time;
    $start2 = $shift2->start_time;
    $end2 = $shift2->end_time;

    // Handle shift yang melewati tengah malam
    if ($end1 < $start1) {
        return ($start2 >= $start1) || ($start2 <= $end1) || 
               ($end2 >= $start1) || ($end2 <= $end1);
    }
    
    if ($end2 < $start2) {
        return ($start1 >= $start2) || ($start1 <= $end2) || 
               ($end1 >= $start2) || ($end1 <= $end2);
    }

    // Shift normal
    return max($start1, $start2) < min($end1, $end2);
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

    public function showStartForm()
    {
        $shifts = Shift::all();
        return view('shifts.start-form', compact('shifts'));
    }

}