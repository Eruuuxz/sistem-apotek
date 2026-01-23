<?php

namespace App\Exports;

use App\Models\Obat;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles; // Tambahan untuk Style
use Maatwebsite\Excel\Concerns\ShouldAutoSize; // Tambahan agar kolom otomatis lebar
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ObatExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
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
            $obat->supplier->nama ?? '-',
            $obat->expired_date,
        ];
    }

    // FUNGSI BARU: Menambahkan garis (border) dan menebalkan header
    public function styles(Worksheet $sheet)
    {
        return [
            // Baris 1 (Header) di-bold
            1 => ['font' => ['bold' => true]],

            // Semua sel yang ada isinya diberi garis tepi (border)
            'A1:' . $sheet->getHighestColumn() . $sheet->getHighestRow() => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => '000000'],
                    ],
                ],
            ],
        ];
    }
}