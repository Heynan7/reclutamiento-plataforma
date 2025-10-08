<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();

            // Información principal
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->string('image')->nullable();

            // Estado
            $table->boolean('is_open')->default(true)->index();

            // Relación con users (admin/reclutador que la creó)
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->cascadeOnDelete()
                ->index();

            // Fechas
            $table->timestamps();     // created_at y updated_at
            $table->softDeletes();    // deleted_at (archivado)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
