<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('surat_pesanan_detail', function (Blueprint $table) {
            // Mengubah kolom harga_satuan agar bisa bernilai NULL dan defaultnya 0
            $table->decimal('harga_satuan', 15, 2)->nullable()->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_pesanan_detail', function (Blueprint $table) {
            // Perintah untuk mengembalikan jika migrasi di-rollback (opsional)
            $table->decimal('harga_satuan', 15, 2)->nullable(false)->change();
        });
    }
};