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
        Schema::table('retur_detail', function (Blueprint $table) {
            // Tambahkan kolom hpp untuk retur penjualan
            $table->decimal('hpp', 15, 2)->nullable()->after('harga');
            // Tambahkan kolom harga_beli untuk retur pembelian
            $table->decimal('harga_beli', 15, 2)->nullable()->after('hpp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('retur_detail', function (Blueprint $table) {
            $table->dropColumn('hpp');
            $table->dropColumn('harga_beli');
        });
    }
};
