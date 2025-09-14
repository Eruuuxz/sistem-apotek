<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('batch_obat', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('obat_id')->index();
            $table->string('no_batch')->nullable()->index();
            $table->date('expired_date')->nullable()->index();
            $table->integer('stok_awal')->default(0);
            $table->integer('stok_saat_ini')->default(0);
            $table->decimal('harga_beli_per_unit', 15, 2)->default(0);
            $table->unsignedBigInteger('supplier_id')->nullable()->index();
            $table->timestamps();

            // FK ke tabel obat — sesuaikan nama tabel jika berbeda
            $table->foreign('obat_id')->references('id')->on('obat')->onDelete('cascade');
            // optional: foreign ke supplier (jika tabel supplier ada)
            $table->foreign('supplier_id')->references('id')->on('supplier')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batch_obat');
    }
};