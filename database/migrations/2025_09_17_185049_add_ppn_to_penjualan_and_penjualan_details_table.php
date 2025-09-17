<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambahkan kolom 'ppn' ke tabel penjualan
        Schema::table('penjualan', function (Blueprint $table) {
            $table->decimal('ppn', 15, 2)->default(0)->after('diskon');
        });

        // Tambahkan kolom 'ppn' ke tabel penjualan_details
        Schema::table('penjualan_details', function (Blueprint $table) {
            $table->decimal('ppn', 15, 2)->default(0)->after('diskon');
        });
    }

    public function down(): void
    {
        Schema::table('penjualan', function (Blueprint $table) {
            $table->dropColumn('ppn');
        });

        Schema::table('penjualan_details', function (Blueprint $table) {
            $table->dropColumn('ppn');
        });
    }
};