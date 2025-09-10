<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cabang', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->nullable()->unique();
            $table->string('nama');
            $table->text('alamat')->nullable();
            $table->boolean('is_pusat')->default(false);
            $table->timestamps();
        });

        // Insert default cabang pusat
        \DB::table('cabang')->insert([
            'kode' => 'PST',
            'nama' => 'Cabang Pusat',
            'alamat' => 'Alamat Pusat',
            'is_pusat' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('cabang');
    }
};