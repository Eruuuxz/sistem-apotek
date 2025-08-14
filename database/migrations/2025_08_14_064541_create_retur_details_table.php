<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('retur_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('retur_id')->constrained('retur')->cascadeOnDelete();
            // Kolom ini akan menyimpan ID dari tabel 'obat' atau 'barang'
            // Tergantung jenis retur (pembelian -> obat, penjualan -> barang)
            $table->unsignedBigInteger('barang_id'); 
            // Anda bisa menambahkan foreign key constraint secara kondisional atau tidak sama sekali
            // Jika ingin strict, bisa buat 2 kolom: obat_id dan barang_id, salah satu nullable
            // Untuk fleksibilitas, kita biarkan unsignedBigInteger saja dan validasi di aplikasi
            
            $table->integer('qty');
            $table->decimal('harga', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('retur_detail');
    }
};