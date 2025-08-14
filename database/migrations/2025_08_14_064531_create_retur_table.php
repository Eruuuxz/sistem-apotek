<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('retur', function (Blueprint $table) {
            $table->id();
            $table->string('no_retur')->unique();
            $table->date('tanggal');
            $table->enum('jenis', ['pembelian', 'penjualan']); // Jenis retur: pembelian atau penjualan
            $table->unsignedBigInteger('transaksi_id'); // ID transaksi sumber (pembelian_id atau penjualan_id)
            $table->decimal('total', 15, 2)->default(0);
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('retur');
    }
};