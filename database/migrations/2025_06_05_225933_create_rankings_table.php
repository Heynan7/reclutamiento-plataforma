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
        Schema::create('rankings', function (Blueprint $table) {
            $table->id();

            // Relación con applications
            $table->foreignId('application_id')
                ->constrained()
                ->onDelete('cascade'); 
                // Si se elimina la postulación, se borra también el ranking

            // Puntaje de afinidad (0–100)
            $table->unsignedTinyInteger('score')->nullable();

            // Análisis en formato JSON (respuesta de la IA)
            $table->json('analysis')->nullable();

            $table->timestamps(); // created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rankings');
    }
};
