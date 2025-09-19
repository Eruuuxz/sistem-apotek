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
        Schema::create('stock_opnames', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_opname_id')->constrained('stock_opnames')->onDelete('cascade');
            $table->foreignId('obat_id')->constrained('obat');
            $table->integer('stok_sistem'); // Stok di sistem saat SO dimulai
            $table->integer('stok_fisik');  // Stok hasil perhitungan fisik
            $table->integer('selisih');     // stok_fisik - stok_sistem
            $table->enum('tipe_penyesuaian', ['penambahan', 'pengurangan', 'tidak_ada'])->default('tidak_ada');
            $table->text('catatan_detail')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_opname_details');
    }
};

