<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PsychotestController extends Controller
{
    /**
     * Recibe las respuestas del psicométrico interno,
     * calcula el puntaje y guarda en la aplicación (siempre guarda; penaliza calidad).
     */
    public function submit(Request $request, Application $application)
    {
        // --- Autorización básica: dueño o admin ---
        $user = $request->user();
        if (!$user || ($user->role !== 'admin' && (int)$application->user_id !== (int)$user->id)) {
            abort(403, 'No autorizado.');
        }

        // --- Validación mínima de payload ---
        $validated = $request->validate([
            'answers'             => 'required|array',
            'meta.start_ts'       => 'required',
            'meta.duration_sec'   => 'required|integer|min:10',
            'meta.straightline'   => 'required|in:0,1',
            'meta.attention_ok'   => 'required|in:0,1',
        ]);

        $answers = $validated['answers'];
        $meta    = $validated['meta'];

        // Normalizar: aceptar keys "likert_raw" o "likert"
        $likert = $answers['likert_raw'] ?? $answers['likert'] ?? [];
        $sjt    = $answers['sjt']        ?? [];

        // --- Mapa de factores (solo servidor) ---
        $map = [
            'C' => ['items' => ['C1','C2','C3','C4'], 'reverse' => ['C2','C4']], // Responsabilidad/Orden
            'T' => ['items' => ['T1','T2','T3','T4'], 'reverse' => ['T2','T4']], // Trabajo en equipo
            'A' => ['items' => ['A1','A2','A3','A4'], 'reverse' => ['A2','A4']], // Adaptabilidad
            'S' => ['items' => ['S1','S2','S3','S4'], 'reverse' => ['S2','S4']], // Tolerancia al estrés
            'I' => ['items' => ['I1','I2','I3','I4'], 'reverse' => ['I2','I4']], // Integridad
            'N' => ['items' => ['N1','N2','N3'],      'reverse' => ['N2']],      // Iniciativa
        ];
        $expectedLikertKeys = array_merge(
            $map['C']['items'], $map['T']['items'], $map['A']['items'],
            $map['S']['items'], $map['I']['items'], $map['N']['items'], ['AA'] // AA = control atención
        );

        // --- Calidad / completitud ---
        $attentionOk = ($meta['attention_ok'] ?? '0') === '1';

        $missing = [];
        foreach ($expectedLikertKeys as $k) {
            if (!array_key_exists($k, $likert)) {
                $missing[] = $k;
            }
        }

        // --- Scoring Likert ---
        [$factorPerc, $likertScore] = $this->scoreLikert($likert, $map);

        // --- Scoring SJT ---
        [$sjtScore, $sjtCorrect, $sjtTotal] = $this->scoreSjt($sjt);

        // --- Penalizaciones de calidad ---
        $duration = (int)$meta['duration_sec'];
        $straight = ($meta['straightline'] ?? '0') === '1';

        $penalty = 0;
        if ($duration < 90)   { $penalty += 7; }   // muy rápido
        if ($straight)        { $penalty += 8; }   // todas iguales (sin contar AA)
        if (!$attentionOk)    { $penalty += 15; }  // atención fallida (no abortamos, penalizamos)
        if (!empty($missing)) { $penalty += 10; }  // faltan ítems
        $penalty = min($penalty, 25);

        // --- Score final (0..100) ---
        $final = (0.70 * $likertScore) + (0.30 * $sjtScore) - $penalty;
        $final = max(0, min(100, round($final, 2)));

        // --- Payload estable para guardar ---
        $payload = [
            'version'      => 'v1.2',
            'factors'      => $factorPerc,     // C,T,A,S,I,N en %
            'likert_score' => $likertScore,    // 0..100
            'sjt_score'    => $sjtScore,       // 0..100
            'sjt_correct'  => $sjtCorrect,
            'sjt_total'    => $sjtTotal,
            'likert_raw'   => $likert,         // respuestas crudas (C1.., AA..)
            'sjt'          => $sjt,            // respuestas crudas SJT (S1..)
            'meta'         => [
                'duration_sec' => $duration,
                'straightline' => $straight,
                'attention_ok' => $attentionOk,
                'penalty'      => $penalty,
                'missing'      => $missing,
                'submitted_at' => now()->toIso8601String(),
            ],
        ];

        // --- Guardar ---
        $application->update([
            'psychotest_score'        => $final,
            'psychotest_answers'      => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE),
            'psychotest_completed_at' => Carbon::now(),
        ]);

        // Redirige a la vista del usuario (o cambia a admin si lo deseas)
        return redirect()
            ->route('user.applications.show', $application)
            ->with('status', '¡Prueba psicométrica completada! Puntaje: '.$final.'/100');
    }

    /**
     * Calcula porcentajes por factor y promedio Likert (0..100).
     * @return array [factorPerc(array), likertScore(float)]
     */
    protected function scoreLikert(array $likert, array $map): array
    {
        $factorScores = [];
        $factorMax    = [];

        foreach ($map as $factor => $cfg) {
            $sum = 0; $count = 0;
            foreach ($cfg['items'] as $item) {
                $v = isset($likert[$item]) ? (int)$likert[$item] : 0;
                if (in_array($item, $cfg['reverse'], true)) {
                    $v = 6 - $v; // invertir 1..5
                }
                $sum += $v; $count++;
            }
            $factorScores[$factor] = $sum;
            $factorMax[$factor]    = $count * 5;
        }

        $factorPerc = [];
        foreach ($factorScores as $f => $sum) {
            $factorPerc[$f] = round(($sum / max(1, $factorMax[$f])) * 100, 2);
        }

        $likertScore = round(array_sum($factorPerc) / max(1, count($factorPerc)), 2);
        return [$factorPerc, $likertScore];
    }

    /**
     * Scoring SJT contra gabarito interno.
     * @return array [sjtScore(0..100), correct(int), total(int)]
     */
    protected function scoreSjt(array $sjt): array
    {
        $keyed = [
            'S1' => 'B','S2' => 'B','S3' => 'B','S4' => 'B','S5' => 'B','S6' => 'B'
        ];
        $correct = 0;
        foreach ($keyed as $k => $right) {
            $ans = $sjt[$k] ?? null;
            if ($ans === $right) $correct++;
        }
        $total = count($keyed);
        $score = round(($correct / max(1, $total)) * 100, 2);
        return [$score, $correct, $total];
    }
}
