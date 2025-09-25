<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CashierShift;
use Carbon\Carbon;

class CheckCashierShift
{
    public function handle(Request $request, Closure $next)
    {
        // Cek role kasir
        if (!Auth::user() || !Auth::user()->hasRole('kasir')) {
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

        // ✅ TAMBAHAN: Cek apakah shift sudah expired
        $now = Carbon::now();
        $shiftDefinition = $activeShift->shift;
        
        // Hitung end time untuk shift aktif
        $shiftEndTime = $activeShift->start_time->copy()
            ->setTimeFromTimeString($shiftDefinition->end_time);
            
        // Jika shift melewati tengah malam (misal: 22:00 - 06:00)
        if ($shiftDefinition->end_time < $shiftDefinition->start_time) {
            $shiftEndTime->addDay();
        }

        // ✅ CEK EXPIRED: Jika waktu sekarang melewati end_time shift
        if ($now->gt($shiftEndTime)) {
            return redirect()->route('pos.index')
                ->with('error', 
                    'Shift Anda (' . $shiftDefinition->name . ') sudah berakhir pada ' . 
                    $shiftEndTime->format('d-m-Y H:i') . '. Silakan akhiri shift dan mulai shift baru jika diperlukan.'
                );
        }

        // ✅ OPTIONAL: Warning jika shift akan berakhir dalam 30 menit
        $warningTime = $shiftEndTime->copy()->subMinutes(30);
        if ($now->gt($warningTime) && $now->lt($shiftEndTime)) {
            session()->flash('warning', 
                'Perhatian: Shift Anda akan berakhir pada ' . 
                $shiftEndTime->format('H:i') . ' (' . 
                $shiftEndTime->diffInMinutes($now) . ' menit lagi).'
            );
        }

        return $next($request);
    }
}
