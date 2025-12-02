<?php

namespace App\Imports;

use App\Models\Obat;
use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class ObatImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // 1. Cek Data Wajib (Skip jika Nama Obat kosong)
        if (empty($row['nama_obat'])) {
            return null;
        }

        // 2. Logika Supplier (Optional)
        // Jika kolom 'nama_supplier' ada isinya, cari ID-nya. Jika tidak, pakai ID 1 (Umum/Default)
        $supplierId = 1; 
        if (!empty($row['nama_supplier'])) {
            $supplier = Supplier::where('nama', 'like', '%' . $row['nama_supplier'] . '%')->first();
            $supplierId = $supplier ? $supplier->id : 1;
        }

        // 3. Logika Kode Obat (Auto-Generate jika kosong)
        // Jika di excel ada 'kode_obat', pakai itu. Jika tidak, generate otomatis.
        $kodeObat = $row['kode_obat'] ?? null;
        
        // Cek apakah obat sudah ada berdasarkan NAMA
        $existingObat = Obat::where('nama', $row['nama_obat'])->first();

        if ($existingObat) {
            // Jika obat sudah ada, gunakan kode yang lama (jangan ditimpa generate baru)
            $kodeObat = $existingObat->kode;
        } elseif (empty($kodeObat)) {
            // Jika obat belum ada DAN kode di excel kosong, buat kode otomatis
            // Contoh format: OBT-3HurufDepan-AngkaAcak (misal: OBT-PAR-482)
            $prefix = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $row['nama_obat']), 0, 3));
            $kodeObat = 'OBT-' . $prefix . '-' . rand(100, 999);
        }

        // 4. Logika Nilai Default (Untuk kolom optional)
        $hargaDasar = isset($row['harga_dasar_hpp']) && is_numeric($row['harga_dasar_hpp']) ? $row['harga_dasar_hpp'] : 0;
        $hargaJual  = isset($row['harga_jual']) && is_numeric($row['harga_jual']) ? $row['harga_jual'] : 0;
        $satuan     = !empty($row['satuan_terkecil']) ? $row['satuan_terkecil'] : 'Pcs';

        // 5. Simpan / Update Data
        return Obat::updateOrCreate(
            ['nama' => $row['nama_obat']], // Kunci pencarian sekarang menggunakan NAMA
            [
                'kode'            => $kodeObat, // Kode hasil logika di atas
                'kategori'        => $row['kategori'] ?? 'Obat Bebas', // Default jika kosong
                'stok'            => $row['stok'] ?? 0,
                'satuan_terkecil' => $satuan,
                
                // Field Optional (Diisi default jika kosong di Excel)
                'harga_dasar'     => $hargaDasar,
                'harga_jual'      => $hargaJual,
                'supplier_id'     => $supplierId,
                'expired_date'    => !empty($row['expired_date']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['expired_date']) : null,
                
                // Nilai Default Database (Tidak perlu ada di Excel)
                'min_stok'        => 5,
                'persen_untung'   => 0,
                'sediaan'         => '-',
                'kemasan_besar'   => '-',
                'rasio_konversi'  => 1
            ]
        );
    }
}