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
        Schema::table('obat', function (Blueprint $table) {
            // Tambahkan kolom baru setelah 'kategori'
            $table->string('sediaan')->nullable()->after('kategori');
            $table->string('kemasan_besar')->nullable()->after('sediaan');
            $table->string('satuan_terkecil')->nullable()->after('kemasan_besar');
            $table->integer('rasio_konversi')->default(1)->after('satuan_terkecil'); // Default 1 untuk obat yang tidak memiliki kemasan besar
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('obat', function (Blueprint $table) {
            $table->dropColumn(['sediaan', 'kemasan_besar', 'satuan_terkecil', 'rasio_konversi']);
        });
    }
};