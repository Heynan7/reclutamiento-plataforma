<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CvAnalyzer;// 👈 corregido (App con A mayúscula)

class TestCvAnalyzer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cv:test {text : Texto de ejemplo del CV}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probar el analizador de CV con un texto simple, sin colas ni PDFs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cvText = $this->argument('text');
        $jobDescription = "Vacante de prueba: Desarrollador PHP Junior con conocimientos en Laravel, MySQL y APIs REST.";

        $this->info("⏳ Analizando texto de prueba con IA...");

        $result = CvAnalyzer::analyze($cvText, $jobDescription);

        $this->line("✅ Resultado:");
        $this->line(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}
