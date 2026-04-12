<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();

            // Usuario que se postula (Candidato)
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Oferta a la que se postula
            $table->foreignId('job_offer_id')
                ->constrained('job_offers')
                ->cascadeOnDelete();

            // Campos adicionales para la postulación
            $table->text('cover_letter')->nullable(); // Carta de presentación
            $table->string('resume_path')->nullable(); // Ruta del CV

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
