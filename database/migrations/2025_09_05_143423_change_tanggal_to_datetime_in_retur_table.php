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
        Schema::table('retur', function (Blueprint $table) {
            // Ubah kolom 'tanggal' dari date menjadi datetime
            $table->dateTime('tanggal')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('retur', function (Blueprint $table) {
            // Kembalikan kolom 'tanggal' dari datetime menjadi date
            $table->date('tanggal')->change();
        });
    }
};