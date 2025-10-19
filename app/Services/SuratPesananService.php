<?php

namespace App\Services;

use App\Models\SuratPesanan;
use App\Models\SuratPesananDetail;
use App\Models\Supplier;
use App\Models\Obat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SuratPesananService
{
    /**
     * Menyimpan Surat Pesanan baru ke database.
     *
     * @param array $data Data request yang sudah divalidasi
     * @return SuratPesanan
     * @throws \Exception
     */
    public function createSuratPesanan(array $data): SuratPesanan
    {
        DB::beginTransaction();
        try {
            $suratPesanan = SuratPesanan::create([
                'no_sp' => $data['no_sp'],
                'tanggal_sp' => $data['tanggal_sp'],
                'supplier_id' => $data['supplier_id'],
                'user_id' => Auth::id(),
                'keterangan' => $data['keterangan'] ?? null,
                'sp_mode' => $data['sp_mode'],
                'jenis_sp' => $data['jenis_sp'],
                'status' => 'pending',
            ]);

            $this->processDetails($suratPesanan, $data);
            
            DB::commit();
            return $suratPesanan;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Memperbarui Surat Pesanan yang sudah ada.
     *
     * @param SuratPesanan $suratPesanan
     * @param array $data Data request yang sudah divalidasi
     * @return SuratPesanan
     * @throws \Exception
     */
    public function updateSuratPesanan(SuratPesanan $suratPesanan, array $data): SuratPesanan
    {
        DB::beginTransaction();
        try {
            $suratPesanan->update($data); // Data yang sudah di-pick di Controller

            $suratPesanan->details()->delete();
            $this->processDetails($suratPesanan, $data);

            DB::commit();
            return $suratPesanan;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Menghapus Surat Pesanan jika belum terkait Pembelian.
     *
     * @param SuratPesanan $suratPesanan
     * @return void
     * @throws \Exception
     */
    public function destroySuratPesanan(SuratPesanan $suratPesanan): void
    {
        DB::beginTransaction();
        try {
            if ($suratPesanan->pembelian()->exists()) {
                throw new \Exception('Surat Pesanan tidak dapat dihapus karena sudah ada pembelian terkait.');
            }

            $suratPesanan->details()->delete();
            $suratPesanan->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Mengambil daftar obat berdasarkan supplier yang dipilih.
     *
     * @param Supplier $supplier
     * @return \Illuminate\Support\Collection
     */
    public function getObatBySupplier(Supplier $supplier): \Illuminate\Support\Collection
    {
        return Obat::where('supplier_id', $supplier->id)
            ->select('id', 'nama', 'stok')
            ->get();
    }
    
    /**
     * Membuat nomor Surat Pesanan baru secara otomatis.
     *
     * @return string
     */
    public function generateNoSp(): string
    {
        $latestSp = SuratPesanan::latest()->first();
        $lastNumber = $latestSp ? (int) Str::afterLast($latestSp->no_sp, '-') : 0;
        return 'SP-' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Helper untuk memproses detail SP (add/update).
     *
     * @param SuratPesanan $suratPesanan
     * @param array $data
     * @return void
     */
    private function processDetails(SuratPesanan $suratPesanan, array $data): void
    {
        if ($data['sp_mode'] === 'dropdown') {
            foreach ($data['obat_id'] as $key => $obatId) {
                SuratPesananDetail::create([
                    'surat_pesanan_id' => $suratPesanan->id,
                    'obat_id' => $obatId,
                    'qty_pesan' => $data['qty_pesan'][$key],
                    'qty_terima' => 0,
                ]);
            }
        } elseif ($data['sp_mode'] === 'manual') {
            foreach ($data['obat_manual'] as $key => $namaManual) {
                SuratPesananDetail::create([
                    'surat_pesanan_id' => $suratPesanan->id,
                    'nama_manual' => $namaManual,
                    'qty_pesan' => $data['qty_pesan'][$key],
                    'qty_terima' => 0,
                ]);
            }
        }
    }
}