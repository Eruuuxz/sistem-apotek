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

        // Cek apakah kasir sudah memiliki shift yang sedang open
        $activeShift = CashierShift::where('user_id', Auth::id())
                                   ->where('status', 'open')
                                   ->first();

        if ($activeShift) {
            return back()->with('error', 'Anda sudah memiliki shift yang sedang berjalan.');
        }

        CashierShift::create([
            'user_id' => Auth::id(),
            'shift_id' => $request->shift_id,
            'start_time' => Carbon::now(),
            'initial_cash' => $request->initial_cash,
            'status' => 'open',
        ]);

        return redirect()->route('pos.index')->with('success', 'Shift berhasil dimulai.');
    }

    /**
     * Mengakhiri shift untuk kasir yang sedang login.
     */
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

        DB::transaction(function () use ($activeShift, $request) {
            // Hitung total penjualan selama shift ini
            $totalSales = Penjualan::where('cashier_shift_id', $activeShift->id)->sum('total');

            $activeShift->update([
                'end_time' => Carbon::now(),
                'final_cash' => $request->final_cash,
                'total_sales' => $totalSales,
                'status' => 'closed',
            ]);
        });

        return redirect()->route('pos.index')->with('success', 'Shift berhasil diakhiri.');
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
