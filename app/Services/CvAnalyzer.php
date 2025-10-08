<?php

namespace App\Services;

use OpenAI;
use Illuminate\Support\Str;

class CvAnalyzer
{
    public static function analyze(string $cvText, string $jobDescription = ''): array
    {
        $client = OpenAI::client(env('OPENAI_API_KEY'));

        // ⚡ Limitar longitud del CV
        $cvText = Str::limit($cvText, 8000, '... [texto truncado]');

        $prompt = <<<PROMPT
Analiza este CV en relación con la vacante descrita más abajo.

Debes evaluar SOLO con base en criterios profesionales:
- Experiencia laboral relacionada con la vacante.
- Formación académica.
- Habilidades técnicas requeridas.
- Habilidades blandas (comunicación, trabajo en equipo, liderazgo).
- Idiomas.
- Certificaciones o cursos relevantes.

⚠️ Importante: No tengas en cuenta género, edad, etnia, religión, orientación sexual ni ningún otro dato personal irrelevante. 
El análisis debe ser justo, objetivo y sin sesgos.

Devuelve ÚNICAMENTE un JSON válido con esta estructura exacta:
{
  "score": número entre 0 y 100,
  "motivos": [
    "bullet point con explicación",
    "bullet point con explicación"
  ],
  "observaciones": "texto breve explicativo"
}

VACANTE:
{$jobDescription}

CV:
{$cvText}
PROMPT;

        try {
            $response = $client->chat()->create([
                'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Eres un evaluador de CVs justo e imparcial. Respondes solo en JSON válido.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ],
                ],
                'response_format' => ['type' => 'json_object'],
                'temperature' => 0.2,
            ]);

            $content = $response->choices[0]->message->content ?? '{}';

            $data = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                \Log::error('Error al decodificar respuesta de IA: ' . json_last_error_msg(), [
                    'content' => $content,
                ]);

                return [
                    'score'   => null,
                    'analysis'=> [
                        'motivos' => ['La IA no devolvió JSON válido'],
                        'observaciones' => 'Verifica logs para más detalle',
                    ],
                ];
            }

            // Validar y limpiar score
            $score = isset($data['score']) ? (int) $data['score'] : null;
            if ($score < 0 || $score > 100) {
                $score = null;
            }

            // Solo guardar motivos y observaciones en analysis
            $analysis = [
                'motivos' => $data['motivos'] ?? [],
                'observaciones' => $data['observaciones'] ?? '',
            ];

            return [
                'score'   => $score,
                'analysis'=> $analysis,
            ];
        } catch (\Throwable $e) {
            \Log::error('Error en CvAnalyzer', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return [
                'score'   => null,
                'analysis'=> [
                    'motivos' => ['Error al procesar el CV'],
                    'observaciones' => $e->getMessage(),
                ],
            ];
        }
    }
}
