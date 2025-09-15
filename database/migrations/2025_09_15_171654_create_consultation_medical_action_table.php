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
        Schema::create('consultation_medical_action', function (Blueprint $table) {
            $table->foreignId('consultation_id')->constrained('consultations')->onDelete('cascade');
            $table->foreignId('medical_action_id')->constrained('medical_actions')->onDelete('cascade');
            $table->decimal('biaya_tindakan_override', 15, 2)->nullable(); // Bisa di-override
            $table->primary(['consultation_id', 'medical_action_id'], 'consultation_medical_action_pk');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultation_medical_action');
    }
};
