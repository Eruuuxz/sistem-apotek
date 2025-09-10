<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pembelian', function (Blueprint $table) {
            $table->unsignedBigInteger('cabang_id')->nullable()->after('supplier_id');
            $table->foreign('cabang_id')->references('id')->on('cabang')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pembelian', function (Blueprint $table) {
            $table->dropForeign(['cabang_id']);
            $table->dropColumn('cabang_id');
        });
    }
};