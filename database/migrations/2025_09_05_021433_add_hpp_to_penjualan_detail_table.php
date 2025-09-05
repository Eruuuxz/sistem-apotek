<?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;  
    return new class extends Migration
    {
        public function up(): void
        {
            Schema::table('penjualan_detail', function (Blueprint $table) {
                $table->decimal('hpp', 15, 2)->after('harga')->default(0);
            });
        }   
        public function down(): void
        {
            Schema::table('penjualan_detail', function (Blueprint $table) {
                $table->dropColumn('hpp');
            });
        }
    };
