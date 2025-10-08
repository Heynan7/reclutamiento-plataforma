<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            🧠 Resultados Psicométricos — {{ $application->user->name }}
        </h2>
    </x-slot>

    @php
        // 1) Decodificar seguro
        $raw = $application->psychotest_answers;
        $answers = is_array($raw) ? $raw : (is_string($raw) ? json_decode($raw, true) : []);
        if (!is_array($answers)) { $answers = []; }

        // 2) Normalizar claves
        $likertRaw   = $answers['likert_raw'] ?? $answers['likert'] ?? [];
        $sjtRaw      = $answers['sjt']        ?? [];
        $factors     = $answers['factors']    ?? [];
        $likertScore = isset($answers['likert_score']) ? (float)$answers['likert_score'] : null;
        $sjtScore    = isset($answers['sjt_score'])    ? (float)$answers['sjt_score']    : null;
        $sjtCorrect  = (int)   ($answers['sjt_correct'] ?? 0);
        $sjtTotal    = (int)   ($answers['sjt_total']   ?? 0);
        $meta        = $answers['meta'] ?? [];
        $version     = $answers['version'] ?? 'v1.0';

        // 3) Fallbacks si faltan puntajes (registros antiguos)
        if ($likertScore === null) {
            // Si tenemos factores, promediamos
            if (is_array($factors) && count($factors)) {
                $sum = 0; $n = 0;
                foreach ($factors as $v) { $sum += (float)$v; $n++; }
                $likertScore = $n ? round($sum / $n, 2) : null;
            }
        }

        if ($sjtScore === null) {
            $gabarito = ['S1'=>'B','S2'=>'B','S3'=>'B','S4'=>'B','S5'=>'B','S6'=>'B'];
            $c = 0; $tot = count($gabarito);
            foreach ($gabarito as $k=>$right) {
                if (($sjtRaw[$k] ?? null) === $right) { $c++; }
            }
            if ($tot) {
                $sjtScore   = round(($c/$tot)*100, 2);
                $sjtCorrect = $c;
                $sjtTotal   = $tot;
            }
        }

        $score = is_null($application->psychotest_score) ? 0.0 : (float)$application->psychotest_score;

        // 4) Flags de calidad
        $flags = [
            'attention_ok' => !empty($meta['attention_ok']),
            'straightline' => !empty($meta['straightline']),
            'fast'         => isset($meta['duration_sec']) && (int)$meta['duration_sec'] < 90,
            'penalty'      => (int) ($meta['penalty'] ?? 0),
        ];

        // Etiquetas de factores
        $labels = [
            'C' => 'Responsabilidad / Orden',
            'T' => 'Trabajo en equipo',
            'A' => 'Adaptabilidad',
            'S' => 'Tolerancia al estrés',
            'I' => 'Integridad',
            'N' => 'Iniciativa',
        ];

        // Gauge
        $pct  = max(0, min(100, $score));
        $circ = 2 * M_PI * 42; // r=42
        $dash = $circ * ($pct/100);
    @endphp

    <div class="max-w-5xl mx-auto bg-white p-6 mt-6 rounded-lg shadow space-y-6">
        {{-- Información general --}}
        <div class="border-b pb-4">
            <h3 class="text-lg font-bold text-gray-800 mb-3">Información general</h3>
            <ul class="text-sm text-gray-700 grid sm:grid-cols-2 gap-2">
                <li><strong>Vacante:</strong> {{ $application->job->title ?? $application->job_title_snapshot }}</li>
                <li><strong>Correo:</strong> {{ $application->user->email }}</li>
                <li><strong>Fecha completado:</strong>
                    {{ $application->psychotest_completed_at ? \Carbon\Carbon::parse($application->psychotest_completed_at)->format('d/m/Y H:i') : '—' }}
                </li>
                <li><strong>Versión:</strong> {{ $version }}</li>
            </ul>
        </div>

        {{-- Puntaje global + secciones --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">
            <div class="relative w-28 h-28">
                <svg viewBox="0 0 100 100" class="w-28 h-28">
                    <circle cx="50" cy="50" r="42" fill="none" stroke="#e5e7eb" stroke-width="10"/>
                    <circle cx="50" cy="50" r="42" fill="none" stroke="#4f46e5" stroke-width="10"
                            stroke-dasharray="{{ $dash }},{{ $circ }}" transform="rotate(-90 50 50)"/>
                    <text x="50" y="54" text-anchor="middle" font-size="20" fill="#111827" font-weight="700">
                        {{ number_format($pct,0) }}
                    </text>
                </svg>
            </div>
            <div class="flex-1">
                <p class="text-gray-800">
                    <span class="font-semibold">Puntaje total:</span>
                    <span class="font-semibold text-indigo-700">{{ number_format($pct,2) }}/100</span>
                </p>
                <p class="text-gray-700 mt-1">
                    <span class="font-semibold">Secciones:</span>
                    Likert {{ $likertScore !== null ? number_format($likertScore,2) : '—' }}/100 ·
                    SJT {{ $sjtScore !== null ? number_format($sjtScore,2) : '—' }}/100
                    ({{ $sjtTotal > 0 ? $sjtCorrect . '/' . $sjtTotal : '—' }} correctas)
                </p>
                <p class="text-gray-700 mt-1">
                    <span class="font-semibold">Calidad:</span>
                    @if(!$flags['attention_ok'])
                        <span class="px-2 py-0.5 bg-red-100 text-red-800 rounded">Atención fallida</span>
                    @endif
                    @if($flags['straightline'])
                        <span class="px-2 py-0.5 bg-yellow-100 text-yellow-800 rounded">Respuestas repetitivas</span>
                    @endif
                    @if($flags['fast'])
                        <span class="px-2 py-0.5 bg-orange-100 text-orange-800 rounded">Duración baja</span>
                    @endif
                    @if(($flags['penalty'] ?? 0) > 0)
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-800 rounded">Penalización: {{ $flags['penalty'] }}</span>
                    @endif
                </p>
            </div>
        </div>

        {{-- Desglose por factores --}}
        <div>
            <h3 class="text-lg font-bold text-gray-800 mb-2">Desglose por factores (0–100)</h3>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($labels as $k => $label)
                    @php $v = isset($factors[$k]) ? max(0,min(100,(float)$factors[$k])) : null; @endphp
                    <div class="border rounded p-3">
                        <div class="flex justify-between text-sm mb-1">
                            <span class="font-medium text-gray-800">{{ $label }}</span>
                            <span class="text-gray-600">{{ $v !== null ? number_format($v,1) : '—' }}</span>
                        </div>
                        <div class="w-full bg-gray-100 h-2 rounded">
                            <div class="h-2 rounded" style="width: {{ $v ?? 0 }}%; background:#4f46e5;"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Metadatos y tiempos --}}
        <div class="text-sm text-gray-700">
            <span class="font-medium">Duración:</span> {{ $meta['duration_sec'] ?? '—' }} s
            <span class="mx-2">•</span>
            <span class="font-medium">Completado:</span>
            {{ $application->psychotest_completed_at ? \Carbon\Carbon::parse($application->psychotest_completed_at)->format('d/m/Y H:i') : '—' }}
        </div>

        {{-- Observaciones automáticas --}}
        @php
            $badge = '';
            $bg = '';
            $txt = '';
            if ($pct >= 80) { $bg='bg-green-50 border-green-500'; $txt='text-green-800'; $badge='Excelente desempeño psicométrico. Alta compatibilidad.'; }
            elseif ($pct >= 50) { $bg='bg-yellow-50 border-yellow-500'; $txt='text-yellow-800'; $badge='Desempeño medio. Requiere validación adicional.'; }
            else { $bg='bg-red-50 border-red-500'; $txt='text-red-800'; $badge='Puntaje bajo. Recomendada evaluación complementaria.'; }
        @endphp
        <div class="mt-2 border-l-4 p-3 rounded {{ $bg }}">
            <p class="text-sm {{ $txt }}">{{ $badge }}</p>
        </div>

        {{-- 🔎 Opción plegable: ver preguntas y respuestas --}}
        <details class="mt-4 group">
            <summary class="cursor-pointer select-none flex items-center justify-between bg-gray-50 hover:bg-gray-100 border rounded px-3 py-2 text-sm text-gray-800">
                <span class="font-medium">Ver preguntas y respuestas del candidato</span>
                <svg class="w-4 h-4 transition-transform group-open:rotate-180" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </summary>

            <div class="mt-3 space-y-6">
                {{-- A) Rasgos (Likert) --}}
                @php
                    $likertMap = [
                        "C1" => "Cumplo mis tareas incluso sin supervisión.",
                        "C2" => "Dejo cosas para último momento.",
                        "C3" => "Mantengo mi trabajo ordenado y documentado.",
                        "C4" => "Me cuesta seguir procesos establecidos.",
                        "T1" => "Disfruto colaborar y compartir información con el equipo.",
                        "T2" => "Prefiero trabajar aislado aunque afecte al grupo.",
                        "T3" => "Escucho y valoro las ideas de otros.",
                        "T4" => "Me irrita tener que coordinar con otros.",
                        "A1" => "Me adapto rápidamente a cambios de prioridades.",
                        "A2" => "Los cambios en el trabajo me desorientan por mucho tiempo.",
                        "A3" => "Aprendo herramientas nuevas con facilidad.",
                        "A4" => "Me cuesta ajustarme cuando cambian los planes.",
                        "S1" => "Tomo decisiones serenas bajo presión.",
                        "S2" => "El estrés me paraliza y no puedo rendir.",
                        "S3" => "En picos de trabajo mantengo la calidad.",
                        "S4" => "Ante plazos cortos me bloqueo.",
                        "I1" => "Haría lo correcto aunque nadie me vea.",
                        "I2" => "Pequeñas trampas son aceptables si ayudan al equipo.",
                        "I3" => "Evito manipular información para parecer mejor.",
                        "I4" => "Si conviene al resultado, ocultaría errores.",
                        "N1" => "Busco mejoras sin que me lo pidan.",
                        "N2" => "Solo hago lo que está en mi descripción de puesto.",
                        "N3" => "Propongo ideas y me pongo en marcha.",
                        "AA" => "Pregunta de control: selecciona 'De acuerdo' (4).",
                    ];
                    $likertAnswers = $likertRaw;
                @endphp

                <div>
                    <h4 class="font-semibold text-gray-800 mb-2 text-sm">A. Rasgos laborales (escala 1–5)</h4>
                    <table class="w-full text-sm border border-gray-200 rounded-lg">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="border px-3 py-2 text-left w-2/3">Pregunta</th>
                                <th class="border px-3 py-2 text-center w-1/3">Respuesta</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($likertMap as $code => $text)
                                <tr class="hover:bg-gray-50">
                                    <td class="border px-3 py-2 text-gray-800">{{ $text }}</td>
                                    <td class="border px-3 py-2 text-center text-gray-700">
                                        {{ $likertAnswers[$code] ?? '—' }}
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="border px-3 py-2 text-center text-gray-500">Sin respuestas registradas.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- B) SJT --}}
                @php
                    $sjtMap = [
                        "S1" => [
                            "Tienes dos tareas urgentes y un compañero te pide ayuda inmediata.",
                            ["A"=>"Ayudas de inmediato y pospones tus tareas.","B"=>"Evalúas prioridades y acuerdas un momento breve para asistir.","C"=>"Dices que no puedes ayudar en ningún caso.","D"=>"Pides a tu jefe que reasigne todas tus tareas."]
                        ],
                        "S2" => [
                            "Notas un error pequeño en un reporte ya enviado al cliente.",
                            ["A"=>"Esperas a ver si el cliente lo detecta.","B"=>"Informas de inmediato el error y envías corrección.","C"=>"Editas el archivo en silencio, sin avisar.","D"=>"Culpas a otro para evitar fricciones."]
                        ],
                        "S3" => [
                            "El plan del proyecto cambia dos días antes de la entrega.",
                            ["A"=>"Te niegas a cambiar porque está 'fuera de alcance'.","B"=>"Actualizas plan y comunicas impacto y riesgos.","C"=>"Sigues como si nada y entregas lo pactado.","D"=>"Trabajas extra sin avisar a nadie de cambios."]
                        ],
                        "S4" => [
                            "Un compañero nuevo comete errores frecuentes.",
                            ["A"=>"Lo criticas en público para que mejore.","B"=>"Le ofreces apoyo y compartes buenas prácticas.","C"=>"Ignoras el tema; no es tu responsabilidad.","D"=>"Informas directamente a dirección sin hablar con él."]
                        ],
                        "S5" => [
                            "Recibes una solicitud ambigua del cliente.",
                            ["A"=>"Asumes lo más probable y avanzas.","B"=>"Pides aclaraciones y confirmas por escrito.","C"=>"Respondes con un presupuesto alto para cubrirte.","D"=>"Rechazas la solicitud por falta de detalles."]
                        ],
                        "S6" => [
                            "La herramienta clave cae 2 horas. ¿Qué haces?",
                            ["A"=>"Esperas a que vuelva; no hay nada que hacer.","B"=>"Buscas alternativas y reordenas tareas críticas.","C"=>"Aprovechas para ponerte al día con el chat.","D"=>"Anuncias que no se podrá cumplir nada hoy."]
                        ],
                    ];
                    $sjtAnswers    = $sjtRaw;
                    $sjtCorrectMap = ['S1'=>'B','S2'=>'B','S3'=>'B','S4'=>'B','S5'=>'B','S6'=>'B'];
                @endphp

                <div>
                    <h4 class="font-semibold text-gray-800 mb-2 text-sm">B. Situational Judgment Test (SJT)</h4>
                    <table class="w-full text-sm border border-gray-200 rounded-lg">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="border px-3 py-2 text-left w-2/5">Escenario</th>
                                <th class="border px-3 py-2 text-left w-2/5">Respuesta del candidato</th>
                                <th class="border px-3 py-2 text-center w-1/5">Respuesta correcta</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sjtMap as $code => [$question, $options])
                                @php
                                    $userAns = $sjtAnswers[$code] ?? null;
                                    $correct = $sjtCorrectMap[$code] ?? null;
                                    $userText = $userAns ? ($options[$userAns] ?? '—') : '—';
                                    $correctText = $options[$correct] ?? '—';
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="border px-3 py-2 text-gray-800">{{ $question }}</td>
                                    <td class="border px-3 py-2 text-gray-700">
                                        {{ $userAns ? "{$userAns}) {$userText}" : '—' }}
                                    </td>
                                    <td class="border px-3 py-2 text-center font-semibold {{ $userAns === $correct ? 'text-green-700' : 'text-red-700' }}">
                                        {{ $correct }}) {{ $correctText }}
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="border px-3 py-2 text-center text-gray-500">Sin respuestas SJT registradas.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </details>

        {{-- Botón volver --}}
        <div class="pt-2">
            <a href="{{ route('admin.jobs.applications', $application->job_id) }}"
               class="inline-flex items-center text-gray-600 text-sm hover:text-indigo-700 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Volver a postulantes
            </a>
        </div>
    </div>
</x-app-layout>
