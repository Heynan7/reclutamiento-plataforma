<x-app-layout>
    <div class="max-w-5xl mx-auto py-10 px-4 sm:px-6 lg:px-8">

        {{-- 🔙 Botón de regreso (ahora vuelve al estado de la postulación) --}}
        <div class="flex justify-between items-center mb-8 flex-wrap gap-3">
            <a href="{{ route('user.applications.show', $application) }}"
               class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-gradient-to-r from-sky-600 to-blue-600 hover:from-sky-700 hover:to-blue-700 text-white font-semibold shadow-md transition">
                ← Volver al estado de mi postulación
            </a>

            <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-800 dark:text-gray-100 leading-tight text-center sm:text-right">
                🧠 Prueba psicométrica  
                <span class="block text-base font-medium text-gray-600 dark:text-gray-400 mt-1">
                    Vacante: {{ $application->job_title_snapshot ?? $application->job->title }}
                </span>
            </h2>
        </div>

        {{-- Contenido principal --}}
        @if($application->psychotest_link)
            {{-- 🔗 Prueba externa --}}
            <div class="bg-white/80 dark:bg-gray-900/70 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm p-8 text-center backdrop-blur">
                <div class="text-gray-700 dark:text-gray-300 mb-5">
                    <p class="text-lg font-medium mb-2">Esta vacante requiere una prueba psicométrica externa.</p>
                    <p class="text-sm">Haz clic en el siguiente botón para abrir la evaluación:</p>
                </div>
                <a href="{{ $application->psychotest_link }}" target="_blank"
                   class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white px-6 py-3 rounded-xl shadow-md text-base font-semibold transition">
                    Abrir prueba psicométrica
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M5 12h14M12 5l7 7-7 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </div>
        @else
            {{-- 🧩 Prueba interna --}}
            <form method="POST" action="{{ route('user.applications.psychotest.submit', $application) }}"
                  class="bg-white/80 dark:bg-gray-900/70 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm p-8 space-y-8 backdrop-blur"
                  id="psychotestForm">
                @csrf

                {{-- Errores --}}
                @if ($errors->any())
                    <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-300 p-4 rounded-lg">
                        <ul class="list-disc list-inside text-sm space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Introducción --}}
                <div class="text-gray-700 dark:text-gray-300 text-sm leading-relaxed">
                    <p class="mb-3">
                        Por favor, responde con sinceridad. Esta evaluación no es clínica ni médica; se utiliza únicamente con fines laborales.
                    </p>
                </div>

                {{-- 🧾 Consentimiento --}}
                <div class="bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl p-5 flex items-start gap-3">
                    <input type="checkbox" id="consent" name="consent"
                           class="mt-1 h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-sky-600 focus:ring-sky-500" required>
                    <label for="consent" class="text-sm text-gray-700 dark:text-gray-300">
                        Confirmo que respondo voluntariamente y con honestidad. Entiendo que mis respuestas se usarán solo dentro del proceso de selección.
                    </label>
                </div>

                {{-- 🧩 Sección A: Rasgos laborales --}}
                <section>
                    <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-2 text-lg">A. Rasgos laborales</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Indica tu grado de acuerdo (1 = Totalmente en desacuerdo, 5 = Totalmente de acuerdo).
                    </p>
                    <div id="likertContainer" class="space-y-4">
                        {{-- Ítems generados dinámicamente por JS --}}
                    </div>
                </section>

                {{-- 🧩 Sección B: SJT --}}
                <section>
                    <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-2 text-lg">B. Situational Judgment Test (SJT)</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Selecciona la opción que consideres más efectiva ante cada situación.
                    </p>
                    <div id="sjtContainer" class="space-y-6">
                        {{-- Escenarios generados dinámicamente por JS --}}
                    </div>
                </section>

                {{-- Campos ocultos --}}
                <input type="hidden" name="meta[start_ts]" id="start_ts">
                <input type="hidden" name="meta[duration_sec]" id="duration_sec">
                <input type="hidden" name="meta[straightline]" id="straightline">
                <input type="hidden" name="meta[attention_ok]" id="attention_ok">

                {{-- Botón de envío --}}
                <div class="text-right">
                    <button type="submit" id="submitBtn"
                            class="inline-flex items-center gap-2 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white px-6 py-2.5 rounded-xl shadow-md font-semibold transition">
                        Enviar respuestas
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M5 12h14M12 5l7 7-7 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>

                <noscript>
                    <p class="mt-4 text-sm text-red-600 dark:text-red-400 font-medium">
                        ⚠️ Debes habilitar JavaScript para completar esta prueba.
                    </p>
                </noscript>
            </form>
        @endif
    </div>

    {{-- Script principal --}}
    @vite(['resources/js/psychotest.js'])

    {{-- Protección de doble envío --}}
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const f = document.getElementById('psychotestForm');
        const btn = document.getElementById('submitBtn');
        if (!f || !btn) return;
        f.addEventListener('submit', () => {
          btn.disabled = true;
          btn.classList.add('opacity-60', 'cursor-not-allowed');
        });
      });
    </script>
</x-app-layout>
