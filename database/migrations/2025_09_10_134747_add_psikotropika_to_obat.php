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
        Schema::table('obat', function (Blueprint $table) {
            $table->boolean('is_psikotropika')->default(false)->after('kategori');
            $table->string('golongan_obat')->nullable()->after('is_psikotropika'); // contoh: "Psikotropika Gol. II"
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('obat', function (Blueprint $table) {
            $table->dropColumn('is_psikotropika');
            $table->dropColumn('golongan_obat');
        });
    }
};