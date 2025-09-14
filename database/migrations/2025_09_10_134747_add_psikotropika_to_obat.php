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
            if (!Schema::hasColumn('obat', 'is_psikotropika')) {
                $table->boolean('is_psikotropika')->default(false)->after('kategori');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('obat', function (Blueprint $table) {
            if (Schema::hasColumn('obat', 'is_psikotropika')) {
                $table->dropColumn('is_psikotropika');
            }
        });
    }
};