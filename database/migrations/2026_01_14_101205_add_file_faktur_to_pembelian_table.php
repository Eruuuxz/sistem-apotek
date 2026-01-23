<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('pembelian', function (Blueprint $table) {
            $table->string('file_faktur')->nullable()->after('no_faktur_pbf');
        });
    }

    public function down()
    {
        Schema::table('pembelian', function (Blueprint $table) {
            $table->dropColumn('file_faktur');
        });
    }
};
