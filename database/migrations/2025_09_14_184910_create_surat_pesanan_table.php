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
        Schema::create('surat_pesanan', function (Blueprint $table) {
            $table->id();
            $table->string('no_sp')->unique();
            $table->dateTime('tanggal_sp');
            $table->foreignId('supplier_id')->constrained('supplier')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // User yang membuat SP
            $table->string('file_template')->nullable(); // Path ke file template SP
            $table->enum('status', ['pending', 'parsial', 'selesai', 'dibatalkan'])->default('pending');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('surat_pesanan_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_pesanan_id')->constrained('surat_pesanan')->onDelete('cascade');
            $table->foreignId('obat_id')->constrained('obat')->onDelete('cascade');
            $table->integer('qty_pesan');
            $table->integer('qty_terima')->default(0); // Jumlah yang sudah diterima
            $table->decimal('harga_satuan', 15, 2); // Harga saat pemesanan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_pesanan_detail');
        Schema::dropIfExists('surat_pesanan');
    }
};
