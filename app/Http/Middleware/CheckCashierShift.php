<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CashierShift;
use App\Models\Shift;

class CheckCashierShift
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Pastikan user adalah kasir
        if (!Auth::check() || !Auth::user()->hasRole('kasir')) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }

        // Cek apakah kasir memiliki shift aktif
        $activeShift = CashierShift::where('user_id', Auth::id())
            ->where('status', 'open')
            ->first();

        if (!$activeShift) {
            // Redirect ke halaman mulai shift jika belum ada shift aktif
            return redirect()->route('shifts.start.form')
                ->with('warning', 'Anda harus memulai shift terlebih dahulu sebelum mengakses POS.');
        }

        return $next($request);
    }
}
