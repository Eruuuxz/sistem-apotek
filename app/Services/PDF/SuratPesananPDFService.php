<?php

namespace App\Services\PDF;

use App\Models\SuratPesanan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Config;

class SuratPesananPDFService
{
    /**
     * Menghasilkan file PDF Surat Pesanan.
     *
     * @param int $id ID Surat Pesanan
     * @return \Illuminate\Http\Response
     */
    public function generatePDF(int $id): \Illuminate\Http\Response
    {
        $suratPesanan = SuratPesanan::with('details.obat', 'supplier', 'user')->findOrFail($id);

        $data = $this->prepareDataForPDF($suratPesanan);
        $viewName = $this->selectTemplate($suratPesanan);

        $pdf = Pdf::loadView($viewName, $data);
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->stream('SP_' . $suratPesanan->no_sp . '.pdf');
    }

    /**
     * Menyiapkan data yang diperlukan untuk view PDF.
     *
     * @param SuratPesanan $suratPesanan
     * @return array
     */
    protected function prepareDataForPDF(SuratPesanan $suratPesanan): array
    {
        return [
            'suratPesanan' => $suratPesanan,
            'clinicData' => Config::get('apotek.clinic'),
            'apotekerData' => Config::get('apotek.apoteker'),
        ];
    }

    /**
     * Menentukan template view PDF mana yang akan digunakan (Reguler atau Prekursor).
     *
     * @param SuratPesanan $suratPesanan
     * @return string Nama view
     */
    protected function selectTemplate(SuratPesanan $suratPesanan): string
    {
        // Pilihan template berdasarkan jenis_sp
        if ($suratPesanan->jenis_sp === 'prekursor') {
             return 'admin.Transaksi.surat_pesanan.pdf_prekursor';
        }
        
        // Cek fallback berdasarkan item detail jika tidak ada jenis_sp yang spesifik
        $containsPrekursorItem = $suratPesanan->details->contains(fn($detail) => $detail->obat && $detail->obat->is_prekursor);
        
        if ($containsPrekursorItem) {
             return 'admin.Transaksi.surat_pesanan.pdf_prekursor';
        }

        return 'admin.Transaksi.surat_pesanan.pdf_regular';
    }
}