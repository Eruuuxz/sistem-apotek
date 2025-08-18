<?php

namespace App\Exports;

use App\Models\Penjualan;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PenjualanExport implements FromView
{
    public function __construct(private ?string $from, private ?string $to) {}

    public function view(): View
    {
        $q = Penjualan::latest();

        if ($this->from) $q->whereDate('tanggal','>=',$this->from);
        if ($this->to)   $q->whereDate('tanggal','<=',$this->to);

        $rows = $q->get();
        $totalAll = $rows->sum('total');

        return view('laporan.penjualan_excel', compact('rows','totalAll'));
    }
}
