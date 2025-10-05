<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pelanggan', function (Blueprint $table) {
            // Menambahkan kolom 'tipe'
            $table->string('tipe')->default('umum')->after('alamat');

            // Menghapus kolom lama jika ada
            if (Schema::hasColumn('pelanggan', 'status_member')) {
                $table->dropColumn('status_member');
            }
            if (Schema::hasColumn('pelanggan', 'point')) {
                $table->dropColumn('point');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pelanggan', function (Blueprint $table) {
            $table->dropColumn('tipe');
            $table->string('status_member')->default('non_member');
            $table->integer('point')->default(0);
        });
    }
};