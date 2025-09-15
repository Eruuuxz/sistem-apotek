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
            $table->boolean('ppn_included')->default(false)->after('harga_dasar'); // Harga dasar sudah termasuk PPN?
            $table->decimal('ppn_rate', 5, 2)->default(0)->after('ppn_included'); // Tarif PPN (e.g., 11.00 untuk 11%)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('obat', function (Blueprint $table) {
            $table->dropColumn(['ppn_included', 'ppn_rate']);
        });
    }
};
