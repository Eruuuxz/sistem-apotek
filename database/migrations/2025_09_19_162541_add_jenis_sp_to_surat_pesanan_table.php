<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
// database/migrations/xxxx_xx_xx_xxxxxx_add_jenis_sp_to_surat_pesanan_table.php

public function up()
{
    Schema::table('surat_pesanan', function (Blueprint $table) {
        $table->enum('jenis_sp', ['reguler', 'prekursor'])->default('reguler')->after('sp_mode');
    });
}

public function down()
{
    Schema::table('surat_pesanan', function (Blueprint $table) {
        $table->dropColumn('jenis_sp');
    });
}
};
