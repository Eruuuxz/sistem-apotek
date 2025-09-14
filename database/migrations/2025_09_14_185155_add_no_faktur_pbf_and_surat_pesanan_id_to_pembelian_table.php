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
        Schema::table('pembelian', function (Blueprint $table) {
            $table->string('no_faktur_pbf')->nullable()->after('no_faktur');
            $table->foreignId('surat_pesanan_id')->nullable()->constrained('surat_pesanan')->onDelete('set null')->after('supplier_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembelian', function (Blueprint $table) {
            $table->dropForeign(['surat_pesanan_id']);
            $table->dropColumn('surat_pesanan_id');
            $table->dropColumn('no_faktur_pbf');
        });
    }
};
