<?php

namespace App\Exports;

use App\Models\Obat;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ObatExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Obat::with('supplier')->get();
    }

    public function headings(): array
    {
        return [
            'Kode Obat',
            'Nama Obat',
            'Kategori',
            'Satuan Terkecil',
            'Stok',
            'Harga Dasar (HPP)',
            'Harga Jual',
            'Nama Supplier',
            'Expired Date (YYYY-MM-DD)',
        ];
    }

    public function map($obat): array
    {
        return [
            $obat->kode,
            $obat->nama,
            $obat->kategori,
            $obat->satuan_terkecil,
            $obat->stok,
            $obat->harga_dasar,
            $obat->harga_jual,
            $obat->supplier->nama ?? '-', // Mengambil nama supplier
            $obat->expired_date,
        ];
    }
}