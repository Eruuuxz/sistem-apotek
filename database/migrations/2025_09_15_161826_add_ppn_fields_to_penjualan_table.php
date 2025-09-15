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
        Schema::table('penjualan', function (Blueprint $table) {
            $table->decimal('ppn_percent', 5, 2)->default(0)->after('diskon_amount'); // Persentase PPN yang dikenakan
            $table->decimal('ppn_amount', 15, 2)->default(0)->after('ppn_percent');   // Jumlah PPN dalam transaksi
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penjualan', function (Blueprint $table) {
            $table->dropColumn(['ppn_percent', 'ppn_amount']);
        });
    }
};
