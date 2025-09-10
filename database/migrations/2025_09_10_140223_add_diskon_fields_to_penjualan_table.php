<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('penjualan', function (Blueprint $table) {
            $table->string('diskon_type', 10)->nullable()->after('total'); // 'fixed' atau 'percent'
            $table->decimal('diskon_value', 15, 2)->default(0)->after('diskon_type'); // nominal atau persen
            $table->decimal('diskon_amount', 15, 2)->default(0)->after('diskon_value'); // nilai diskon terhitung
        });
    }

    public function down(): void
    {
        Schema::table('penjualan', function (Blueprint $table) {
            $table->dropColumn(['diskon_type', 'diskon_value', 'diskon_amount']);
        });
    }
};