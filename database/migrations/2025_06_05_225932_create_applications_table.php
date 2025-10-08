<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('job_id')->constrained()->cascadeOnDelete();

            $table->text('cover_letter')->nullable();
            $table->string('cv_file')->nullable();
            $table->string('job_title_snapshot')->nullable();

            // Estado general
            $table->string('status')->default('submitted')->index();
            $table->timestamp('status_updated_at')->nullable();

            // Entrevistas
            $table->timestamp('interview_at')->nullable();
            $table->string('interview_channel')->nullable();
            $table->string('interview_link')->nullable();
            $table->string('interview_location')->nullable();
            $table->text('interview_message')->nullable();

            // Disponibilidad
            $table->text('availability_response')->nullable();
            $table->timestamp('availability_confirmed_at')->nullable();

            // Psychotest
            $table->decimal('psychotest_score', 5, 2)->nullable();
            $table->json('psychotest_answers')->nullable();
            $table->timestamp('psychotest_completed_at')->nullable();
            $table->string('psychotest_link')->nullable();

            // Estudio socioeconómico
            $table->string('socioeconomic_link')->nullable();
            $table->text('socioeconomic_notes')->nullable();
            $table->timestamp('socioeconomic_completed_at')->nullable();

            // Lectura
            $table->timestamp('read_at')->nullable();

            $table->timestamps();

            // 👉 Evita postulación duplicada del mismo usuario a la misma vacante
            $table->unique(['user_id', 'job_id']);

            // Índices útiles
            $table->index(['job_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
