<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Obat; // Pastikan model Obat sudah di-import

class UpdateExistingObatPackaging extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'obat:update-packaging';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update existing obat records with default packaging and conversion ratios based on sediaan.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting update for existing obat packaging...');

        $obats = Obat::all();
        $updatedCount = 0;
        $skippedCount = 0;
        $manualReviewCount = 0;

        // Definisi pemetaan sediaan ke satuan_terkecil default
        $sediaanToSatuanTerkecil = [
            'Tablet'        => 'Tablet',
            'Kapsul'        => 'Kapsul',
            'Sirup'         => 'ml', // Atau 'Botol' jika penjualan selalu per botol
            'Salep'         => 'gram', // Atau 'Tube'
            'Injeksi'       => 'Ampul', // Atau 'Vial'
            'Tetes Mata'    => 'ml', // Atau 'Botol'
            'Tetes Telinga' => 'ml', // Atau 'Botol'
            'Suppositoria'  => 'Suppositoria',
            'Suspensi'      => 'ml',
            'Cream'         => 'gram',
            'Gel'           => 'gram',
            'Serbuk'        => 'sachet', // Atau 'gram'
            'Lain-lain'     => 'unit', // Default paling umum
        ];

        // Definisi pemetaan kemasan_besar dan rasio_konversi default
        // Ini adalah nilai default yang paling umum, mungkin perlu penyesuaian manual
        $defaultPackaging = [
            'Tablet'        => ['kemasan_besar' => 'Strip', 'rasio_konversi' => 10],
            'Kapsul'        => ['kemasan_besar' => 'Strip', 'rasio_konversi' => 10],
            'ml'            => ['kemasan_besar' => 'Botol', 'rasio_konversi' => 1], // 1 Botol = 1 ml (jika ml adalah satuan terkecil)
            'gram'          => ['kemasan_besar' => 'Tube', 'rasio_konversi' => 1], // 1 Tube = 1 gram (jika gram adalah satuan terkecil)
            'Ampul'         => ['kemasan_besar' => 'Box', 'rasio_konversi' => 5], // Contoh: 1 Box = 5 Ampul
            'Vial'          => ['kemasan_besar' => 'Box', 'rasio_konversi' => 1], // Contoh: 1 Box = 1 Vial
            'Suppositoria'  => ['kemasan_besar' => 'Strip', 'rasio_konversi' => 6], // Contoh: 1 Strip = 6 Suppositoria
            'sachet'        => ['kemasan_besar' => 'Box', 'rasio_konversi' => 30], // Contoh: 1 Box = 30 sachet
            'unit'          => ['kemasan_besar' => 'Pcs', 'rasio_konversi' => 1], // Default untuk 'Lain-lain'
        ];

        foreach ($obats as $obat) {
            $needsUpdate = false;
            $logMessage = "Processing Obat: {$obat->nama} (ID: {$obat->id})";

            // 1. Tentukan satuan_terkecil
            if (is_null($obat->satuan_terkecil) || empty($obat->satuan_terkecil)) {
                $obat->satuan_terkecil = $sediaanToSatuanTerkecil[$obat->sediaan] ?? 'unit';
                $logMessage .= " | Set satuan_terkecil to '{$obat->satuan_terkecil}' (from sediaan)";
                $needsUpdate = true;
            } else {
                $logMessage .= " | Existing satuan_terkecil: '{$obat->satuan_terkecil}'";
            }

            // 2. Tentukan kemasan_besar dan rasio_konversi berdasarkan satuan_terkecil yang sudah ada/ditentukan
            $currentSatuanTerkecil = $obat->satuan_terkecil;
            $packagingInfo = $defaultPackaging[$currentSatuanTerkecil] ?? null;

            if ($packagingInfo) {
                if (is_null($obat->kemasan_besar) || empty($obat->kemasan_besar)) {
                    $obat->kemasan_besar = $packagingInfo['kemasan_besar'];
                    $logMessage .= " | Set kemasan_besar to '{$obat->kemasan_besar}'";
                    $needsUpdate = true;
                } else {
                    $logMessage .= " | Existing kemasan_besar: '{$obat->kemasan_besar}'";
                }

                if (is_null($obat->rasio_konversi) || $obat->rasio_konversi === 0) {
                    $obat->rasio_konversi = $packagingInfo['rasio_konversi'];
                    $logMessage .= " | Set rasio_konversi to '{$obat->rasio_konversi}'";
                    $needsUpdate = true;
                } else {
                    $logMessage .= " | Existing rasio_konversi: '{$obat->rasio_konversi}'";
                }
            } else {
                // Jika tidak ada default packaging info, tandai untuk review manual
                $logMessage .= " | WARNING: No default packaging info found for satuan_terkecil '{$currentSatuanTerkecil}'. Manual review needed.";
                $manualReviewCount++;
            }

            // 3. Verifikasi Stok (Asumsi stok yang ada sudah dalam satuan terkecil)
            // Jika Anda memiliki data lama yang menunjukkan stok dalam kemasan besar,
            // Anda perlu menambahkan logika konversi di sini.
            // Contoh: if ($obat->old_stock_unit == $obat->kemasan_besar) { $obat->stok *= $obat->rasio_konversi; }
            // Untuk saat ini, kita tidak mengubah nilai stok yang ada.

            if ($needsUpdate) {
                $obat->save();
                $updatedCount++;
                $this->line($logMessage . " -> SAVED");
            } else {
                $skippedCount++;
                $this->line($logMessage . " -> SKIPPED (no changes needed)");
            }
        }

        $this->info("----------------------------------------------------");
        $this->info("Finished updating existing obat packaging.");
        $this->info("Total records processed: " . ($updatedCount + $skippedCount));
        $this->info("Records updated: {$updatedCount}");
        $this->info("Records skipped (already had values): {$skippedCount}");
        if ($manualReviewCount > 0) {
            $this->warn("Records needing manual review: {$manualReviewCount} (check logs for 'WARNING')");
        }
        $this->info("----------------------------------------------------");
    }
}
