<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Tambahkan ini jika perlu drop index manual

return new class extends Migration {
    public function up(): void
    {
        Schema::table('obat', function (Blueprint $table) {
            if (Schema::hasColumn('obat', 'nama')) {
                $table->index('nama');
            }
            if (Schema::hasColumn('obat', 'kode')) {
                $table->index('kode');
            }
            if (Schema::hasColumn('obat', 'kategori')) {
                $table->index('kategori');
            }
        });
    }

    public function down(): void
    {
        Schema::table('obat', function (Blueprint $table) {
            if (Schema::hasColumn('obat', 'nama')) {
                $table->dropIndex(['nama']);
            }
            if (Schema::hasColumn('obat', 'kode')) {
                $table->dropIndex(['kode']);
            }
            if (Schema::hasColumn('obat', 'kategori')) {
                $table->dropIndex(['kategori']);
            }
        });
    }
};