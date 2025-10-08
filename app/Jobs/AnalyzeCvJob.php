<?php

namespace App\Jobs;

use App\Models\Application;
use App\Models\Ranking;
use App\Services\CvTextExtractor;
use App\Services\CvAnalyzer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AnalyzeCvJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Número de intentos si falla.
     */
    public $tries = 3;

    /**
     * Tiempo máximo por intento (segundos).
     */
    public $timeout = 120;

    /**
     * ID de la aplicación a analizar.
     */
    public int $applicationId;

    /**
     * Crear nueva instancia del Job.
     */
    public function __construct(int $applicationId)
    {
        $this->applicationId = $applicationId;
    }

    /**
     * Ejecutar el Job.
     */
    public function handle(): void
    {
        Log::info("🚀 Iniciando AnalyzeCvJob para application_id={$this->applicationId}");

        $application = Application::with('job')->find($this->applicationId);
        if (!$application) {
            Log::warning("⚠️ No se encontró la aplicación {$this->applicationId}");
            return;
        }

        if (!$application->cv_file) {
            Log::warning("⚠️ La aplicación {$this->applicationId} no tiene CV adjunto");
            return;
        }

        try {
            // 📜 Extraer texto del CV
            $cvText = CvTextExtractor::extract($application->cv_file);
            $jobDescription = $application->job?->description ?? '';

            // 🤖 Analizar con IA
            $result = CvAnalyzer::analyze($cvText, $jobDescription);

            Log::info("📥 Respuesta IA cruda", ['result' => $result]);

            // ✅ Validar y normalizar score
            $score = isset($result['score']) ? (int) $result['score'] : null;
            if ($score < 0 || $score > 100) {
                Log::warning("⚠️ Score fuera de rango", [
                    'application_id' => $application->id,
                    'score_raw' => $result['score'] ?? null,
                ]);
                $score = null;
            }

            // ✅ Normalizar analysis
            $analysis = $result['analysis'] ?? [];
            if (!is_array($analysis)) {
                $analysis = ['observaciones' => (string) $analysis];
            }

            // 📊 Guardar en DB (Laravel hace cast → JSON automático)
            Ranking::updateOrCreate(
                ['application_id' => $application->id],
                [
                    'score'   => $score,
                    'analysis'=> $analysis,
                ]
            );

            Log::info("✅ Análisis IA completado", [
                'application_id' => $this->applicationId,
                'score'          => $score,
            ]);
        } catch (\Throwable $e) {
            Log::error("❌ Error en AnalyzeCvJob", [
                'application_id' => $this->applicationId,
                'message'        => $e->getMessage(),
                'trace'          => $e->getTraceAsString(),
            ]);

            // ⚠️ Guardar fallback en DB para no dejarlo 'Pendiente'
            Ranking::updateOrCreate(
                ['application_id' => $application->id],
                [
                    'score'   => null,
                    'analysis'=> [
                        'error' => 'Error en el análisis automático',
                        'detalles' => $e->getMessage(),
                    ],
                ]
            );
        }
    }
}
