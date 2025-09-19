<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pembelian_detail', function (Blueprint $table) {
            // Pastikan kolom-kolom ini ada dan sesuai
            // no_batch dan expired_date sudah ada di migrasi ini, kita hanya memastikan
            if (!Schema::hasColumn('pembelian_detail', 'no_batch')) {
                $table->string('no_batch')->nullable()->after('harga_beli');
            }
            if (!Schema::hasColumn('pembelian_detail', 'expired_date')) {
                $table->date('expired_date')->nullable()->after('no_batch');
            }
            // Hapus kolom yang tidak diperlukan atau duplikat
            if (Schema::hasColumn('pembelian_detail', 'harga_beli_satuan')) {
                $table->dropColumn('harga_beli_satuan');
            }
            if (Schema::hasColumn('pembelian_detail', 'batch_id')) {
                $table->dropColumn('batch_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pembelian_detail', function (Blueprint $table) {
            if (Schema::hasColumn('pembelian_detail', 'no_batch')) {
                $table->dropColumn('no_batch');
            }
            if (Schema::hasColumn('pembelian_detail', 'expired_date')) {
                $table->dropColumn('expired_date');
            }
            // Tambahkan kembali jika diperlukan untuk rollback, atau biarkan kosong jika tidak ada data yang hilang
            // $table->decimal('harga_beli_satuan', 15, 2)->default(0)->after('expired_date');
            // $table->unsignedBigInteger('batch_id')->nullable()->after('harga_beli_satuan');
        });
    }
};
