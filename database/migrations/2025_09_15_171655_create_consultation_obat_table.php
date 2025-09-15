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
        Schema::create('consultation_obat', function (Blueprint $table) {
            $table->foreignId('consultation_id')->constrained('consultations')->onDelete('cascade');
            $table->foreignId('obat_id')->constrained('obats')->onDelete('cascade');
            $table->integer('qty');
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->primary(['consultation_id', 'obat_id'], 'consultation_obat_pk');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultation_obat');
    }
};
