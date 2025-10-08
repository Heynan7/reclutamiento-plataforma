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
        Schema::create('queue_jobs', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Nombre de la cola (default u otra)
            $table->string('queue')->index();

            // Payload serializado del job
            $table->longText('payload');

            // Número de intentos
            $table->unsignedTinyInteger('attempts');

            // Control de reservas (timestamps UNIX)
            $table->bigInteger('reserved_at')->nullable();
            $table->bigInteger('available_at');
            $table->bigInteger('created_at');

            // Índice adicional para mejorar rendimiento
            $table->index(['queue', 'reserved_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queue_jobs');
    }
};
