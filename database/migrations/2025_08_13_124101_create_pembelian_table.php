<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembelian', function (Blueprint $table) {
            $table->id();
            $table->string('no_faktur')->unique(); // Ubah dari nomor_faktur ke no_faktur
            $table->date('tanggal');
            $table->foreignId('supplier_id')->nullable()->constrained('supplier')->onDelete('set null'); // Tambahkan ini
            $table->decimal('total', 15, 2)->default(0); // Tambahkan ini
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembelian');
    }
};