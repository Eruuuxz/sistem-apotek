<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penjualan', function (Blueprint $table) {
            $table->id();
            $table->string('no_nota')->unique();
            $table->date('tanggal');
            // $table->string('kasir_nama');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Tambahkan ini
            $table->decimal('total', 15, 2)->default(0);
            $table->decimal('bayar', 15, 2)->default(0); // Tambahkan ini
            $table->decimal('kembalian', 15, 2)->default(0); // Tambahkan ini
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualan');
    }
};