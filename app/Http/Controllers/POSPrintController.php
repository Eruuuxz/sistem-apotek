<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use Barryvdh\DomPDF\Facade\Pdf;

class POSPrintController extends Controller
{
    /**
     * Menampilkan opsi cetak setelah checkout.
     */
    public function printOptions($id)
    {
        $penjualan = Penjualan::findOrFail($id);
        return view('kasir.print-options', compact('penjualan'));
    }

    /**
     * Mencetak Faktur (Struk Thermal).
     */
    public function printFaktur($id)
    {
        $penjualan = Penjualan::with('details.obat', 'kasir', 'pelanggan')->findOrFail($id);
        return view('kasir.struk', compact('penjualan'));
    }

    /**
     * Mencetak Invoice (A4/Legal).
     */
    public function printInvoice($id)
    {
        $penjualan = Penjualan::with('details.obat', 'kasir', 'pelanggan')->findOrFail($id);
        return view('kasir.invoice', compact('penjualan'));
    }
    
    // Metode cetak PDF yang sebelumnya ada di POSController
    public function strukPdf($id)
    {
        $penjualan = Penjualan::with('details.obat', 'kasir', 'pelanggan')->findOrFail($id);
        // Menggunakan library DomPDF
        $pdf = Pdf::loadView('kasir.struk', compact('penjualan'))->setPaper('A6', 'landscape');
        return $pdf->stream('faktur-' . $penjualan->no_nota . '.pdf');
    }
}