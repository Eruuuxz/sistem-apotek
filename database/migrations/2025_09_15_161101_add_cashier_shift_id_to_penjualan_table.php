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
        Schema::table('penjualan', function (Blueprint $table) {
            // Menambahkan kolom cashier_shift_id setelah user_id
            $table->foreignId('cashier_shift_id')->nullable()->constrained('cashier_shifts')->onDelete('set null')->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penjualan', function (Blueprint $table) {
            $table->dropForeign(['cashier_shift_id']);
            $table->dropColumn('cashier_shift_id');
        });
    }
};
