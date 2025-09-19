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
        Schema::table('pembelian', function (Blueprint $table) {
            if (!Schema::hasColumn('pembelian', 'ppn_amount')) {
                $table->decimal('ppn_amount', 15, 2)->default(0)->after('total'); // Total PPN untuk pembelian ini
            }
            if (!Schema::hasColumn('pembelian', 'status')) {
                $table->enum('status', ['draft', 'final', 'cancelled'])->default('draft')->after('ppn_amount');
            }
            if (!Schema::hasColumn('pembelian', 'diskon')) {
                $table->decimal('diskon', 15, 2)->default(0)->after('total');
            }
            if (!Schema::hasColumn('pembelian', 'diskon_type')) {
                $table->enum('diskon_type', ['nominal', 'persen'])->default('nominal')->after('diskon');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembelian', function (Blueprint $table) {
            if (Schema::hasColumn('pembelian', 'ppn_amount')) {
                $table->dropColumn('ppn_amount');
            }
            if (Schema::hasColumn('pembelian', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('pembelian', 'diskon')) {
                $table->dropColumn('diskon');
            }
            if (Schema::hasColumn('pembelian', 'diskon_type')) {
                $table->dropColumn('diskon_type');
            }
        });
    }
};