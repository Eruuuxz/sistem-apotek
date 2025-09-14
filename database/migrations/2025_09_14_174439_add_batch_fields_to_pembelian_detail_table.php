<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pembelian_detail', function (Blueprint $table) {
            $table->string('no_batch')->nullable()->after('harga_beli'); // sesuaikan posisi kolom 'harga_beli' jika beda
            $table->date('expired_date')->nullable()->after('no_batch');
            $table->decimal('harga_beli_satuan', 15, 2)->default(0)->after('expired_date');
            $table->unsignedBigInteger('batch_id')->nullable()->after('harga_beli_satuan');
        });
    }

    public function down(): void
    {
        Schema::table('pembelian_detail', function (Blueprint $table) {
            $table->dropColumn(['no_batch', 'expired_date', 'harga_beli_satuan']);
            $table->dropColumn('batch_id');
        });
    }
};