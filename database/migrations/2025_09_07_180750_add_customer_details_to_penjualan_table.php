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
            $table->string('nama_pelanggan')->nullable()->after('kembalian');
            $table->string('alamat_pelanggan')->nullable()->after('nama_pelanggan');
            $table->string('telepon_pelanggan')->nullable()->after('alamat_pelanggan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penjualan', function (Blueprint $table) {
            $table->dropColumn('nama_pelanggan');
            $table->dropColumn('alamat_pelanggan');
            $table->dropColumn('telepon_pelanggan');
        });
    }
};