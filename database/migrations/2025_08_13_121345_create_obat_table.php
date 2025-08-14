<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('obat', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->string('kategori')->nullable();
            $table->integer('stok')->default(0);
            $table->decimal('harga_dasar', 15, 2);
            $table->decimal('persen_untung', 5, 2);
            $table->decimal('harga_jual', 15, 2);
            $table->foreignId('supplier_id')->nullable()->constrained('supplier')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('obat');
    }
};
