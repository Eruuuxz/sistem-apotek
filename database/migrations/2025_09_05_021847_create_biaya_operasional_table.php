<?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;  
    return new class extends Migration
    {
        public function up(): void
        {
            Schema::create('biaya_operasional', function (Blueprint $table) {
                $table->id();
                $table->date('tanggal');
                $table->string('jenis_biaya'); // Contoh: Gaji, Listrik, Sewa, Air, Internet
                $table->decimal('jumlah', 15, 2);
                $table->text('keterangan')->nullable();
                $table->timestamps();
            });
        }   
        public function down(): void
        {
            Schema::dropIfExists('biaya_operasional');
        }
    };
    