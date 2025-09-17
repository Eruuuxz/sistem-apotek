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
    // Dalam ShiftController.php

public function endShift(Request $request)
{
    $request->validate([
        'final_cash' => 'required|numeric|min:0',
    ]);

    $activeShift = CashierShift::where('user_id', Auth::id())
                               ->where('status', 'open')
                               ->first();

    if (!$activeShift) {
        return back()->with('error', 'Anda tidak memiliki shift yang sedang berjalan.');
    }

    $activeShift->update([
        'end_time' => Carbon::now(),
        'final_cash' => $request->final_cash,
        // Hapus 'total_sales' di sini, karena sudah dihitung di POSController
        'status' => 'closed',
    ]);

    // Opsional: Redirect ke halaman login setelah shift selesai
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login')->with('success', 'Shift Anda berhasil diakhiri.');
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
        
        // Kasir hanya bisa melihat shiftnya sendiri
        $query->where('user_id', Auth::id()); 

        $cashierShifts = $query->latest('start_time')->paginate(10);
        $users = collect(); // Untuk kasir, tidak perlu daftar user lain

        return view('kasir.summary', compact('cashierShifts', 'users'));
    }
}
