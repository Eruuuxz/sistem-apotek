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
        Schema::create('cashier_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Kasir
            $table->foreignId('shift_id')->constrained('shifts')->onDelete('cascade'); // Jenis shift
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable(); // Nullable karena shift bisa belum berakhir
            $table->decimal('initial_cash', 15, 2); // Modal awal
            $table->decimal('final_cash', 15, 2)->nullable(); // Uang akhir (saat shift ditutup)
            $table->decimal('total_sales', 15, 2)->default(0); // Total penjualan selama shift
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashier_shifts');
    }
};
