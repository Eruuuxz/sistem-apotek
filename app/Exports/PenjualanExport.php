<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;

class PenjualanExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $data;
    protected $periode;

    public function __construct($data, $periode)
    {
        $this->data = $data;
        $this->periode = $periode;
    }

    public function collection()
    {
        return $this->data;
    }

    // Judul Kolom
    public function headings(): array
    {
        return [
            'No Nota',
            'Tanggal',
            'Pelanggan',
            'Total (Rp)',
            'Kasir',
        ];
    }

    // Isi Data
    public function map($row): array
    {
        return [
            $row->no_nota,
            Carbon::parse($row->tanggal)->format('d-m-Y H:i'),
            $row->nama_pelanggan ?? $row->pelanggan->nama ?? 'Umum',
            $row->total,
            $row->kasir->name ?? '-',
        ];
    }

    // Styling Garis & Header
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A1:' . $sheet->getHighestColumn() . $sheet->getHighestRow() => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ],
        ];
    }
}