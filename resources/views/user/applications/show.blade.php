<x-app-layout>
    <div class="max-w-3xl mx-auto py-10 px-4 sm:px-6 lg:px-8">

        {{-- 🔙 Botón volver arriba --}}
        <div class="flex justify-start mb-6">
            <a href="{{ route('user.applications.index') }}"
               class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-gradient-to-r from-sky-600 to-blue-600 hover:from-sky-700 hover:to-blue-700 text-white font-semibold shadow-md transition">
                ← Volver a mis postulaciones
            </a>
        </div>

        {{-- 🧾 Encabezado --}}
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-extrabold text-gray-800 dark:text-gray-100">🧾 Estado de tu postulación</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">
                Vacante: <span class="font-semibold text-sky-700 dark:text-sky-400">{{ $application->job->title ?? $application->job_title_snapshot }}</span>
            </p>
        </div>

        {{-- 🔹 Timeline --}}
        <div class="relative border-l border-gray-300 dark:border-gray-700 ml-3 space-y-8">

            @php
                $steps = [
                    ['id' => 'submitted', 'icon' => '📄', 'title' => 'Recibido', 'color' => 'blue'],
                    ['id' => 'shortlisted', 'icon' => '⭐', 'title' => 'Preseleccionado', 'color' => 'yellow'],
                    ['id' => 'interview_scheduled', 'icon' => '👔', 'title' => 'Entrevista preliminar', 'color' => 'purple'],
                    ['id' => 'psychotest', 'icon' => '🧠', 'title' => 'Pruebas psicométricas', 'color' => 'pink'],
                    ['id' => 'interview_deep', 'icon' => '🎤', 'title' => 'Entrevista profunda', 'color' => 'indigo'],
                    ['id' => 'socioeconomic_study', 'icon' => '🏠', 'title' => 'Estudio socioeconómico', 'color' => 'orange'],
                    ['id' => 'hired', 'icon' => '✅', 'title' => 'Contratado', 'color' => 'green'],
                    ['id' => 'rejected', 'icon' => '❌', 'title' => 'No contratado', 'color' => 'red'],
                    ['id' => 'closed', 'icon' => '📌', 'title' => 'Cerrado', 'color' => 'gray'],
                ];
            @endphp

            @foreach ($steps as $step)
                @php
                    $isActive = $application->status === $step['id'];
                    $bg = $isActive
                        ? "bg-{$step['color']}-100 dark:bg-{$step['color']}-900/30 border-{$step['color']}-400 dark:border-{$step['color']}-600"
                        : "bg-gray-50 dark:bg-gray-800/50 border-gray-200 dark:border-gray-700";
                @endphp

                <div class="relative pl-8">
                    <!-- Línea y punto -->
                    <span class="absolute -left-[9px] top-4 h-3 w-3 rounded-full {{ $isActive ? 'bg-sky-500' : 'bg-gray-400 dark:bg-gray-600' }}"></span>

                    <!-- Card -->
                    <div class="border {{ $bg }} rounded-xl shadow-sm p-4 transition-all hover:shadow-md">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-lg">{{ $step['icon'] }}</span>
                            <h3 class="font-semibold text-gray-800 dark:text-gray-100">{{ $step['title'] }}</h3>
                            @if ($isActive)
                                <span class="ml-auto inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-300">
                                    Actual
                                </span>
                            @endif
                        </div>

                        {{-- Detalles cuando está activo --}}
                        @if ($isActive)
                            <div class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed space-y-2">
                                {{-- 👔 Entrevista preliminar / profunda --}}
                                @if(in_array($step['id'], ['interview_scheduled', 'interview_deep']))
                                    <p><strong>📅 Fecha y hora:</strong> {{ $application->interview_at ? \Carbon\Carbon::parse($application->interview_at)->format('d/m/Y H:i') : 'Pendiente' }}</p>
                                    <p><strong>📍 Medio:</strong> {{ $application->interview_channel ?? 'Pendiente' }}</p>

{{-- 🔗 Enlace de entrevista --}}
@if($application->interview_link)
    @php
        // Verificar si es una URL válida
        $isUrl = filter_var($application->interview_link, FILTER_VALIDATE_URL);
        $label = $isUrl
            ? (parse_url($application->interview_link, PHP_URL_HOST) ?? 'Abrir enlace')
            : $application->interview_link;
    @endphp

    <p><strong>🔗 Enlace:</strong>
        @if($isUrl)
            <a href="{{ $application->interview_link }}"
               target="_blank"
               class="text-sky-600 dark:text-sky-400 underline break-words hover:text-sky-700">
               {{ $label }}
            </a>
        @else
            <span class="text-gray-800 dark:text-gray-200">{{ $label }}</span>
        @endif
    </p>
@endif


                                    {{-- 💬 Mensaje --}}
                                    @if($application->interview_message)
                                        <div class="mt-3 bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-3 rounded">
                                            <p><strong>💬 Mensaje del reclutador:</strong><br>{{ $application->interview_message }}</p>
                                        </div>
                                    @endif

                                    {{-- ✅ Confirmación --}}
                                    @include('components.applications.confirmation-buttons', ['application' => $application])
                                @endif

                                {{-- 🧠 Psicotest --}}
                                @if($step['id'] === 'psychotest')
                                    @if($application->psychotest_link)
                                        <a href="{{ $application->psychotest_link }}" target="_blank"
                                           class="inline-block mt-2 px-4 py-2 rounded-lg bg-pink-600 hover:bg-pink-700 text-white text-sm font-medium transition">
                                           Abrir prueba psicométrica externa
                                        </a>
                                    @elseif($application->psychotest_completed_at)
                                        <p class="font-semibold text-green-600 dark:text-green-400">
                                            ✅ Prueba completada — Puntaje: {{ $application->psychotest_score }}/100
                                        </p>
                                    @else
                                        <a href="{{ route('user.applications.psychotest', $application->id) }}"
                                           class="inline-block mt-2 px-4 py-2 rounded-lg bg-pink-600 hover:bg-pink-700 text-white text-sm font-medium transition">
                                           Realizar prueba psicométrica interna
                                        </a>
                                    @endif
                                @endif

                                {{-- 🏠 Estudio socioeconómico --}}
                                @if($step['id'] === 'socioeconomic_study' && $application->socioeconomic_link)
                                    @php [$label, $href] = $renderLink($application->socioeconomic_link); @endphp
                                    <p><strong>🔗 Enlace al estudio:</strong>
                                        @if($href)
                                            <a href="{{ $href }}" target="_blank" class="text-sky-600 dark:text-sky-400 underline break-words">{{ $label }}</a>
                                        @else
                                            <span>{{ $label }}</span>
                                        @endif
                                    </p>
                                    @include('components.applications.confirmation-buttons', ['application' => $application])
                                @endif

                                {{-- ✅ Contratado / ❌ Rechazado --}}
                                @if(in_array($step['id'], ['hired', 'rejected']))
                                    <p class="font-medium {{ $step['id'] === 'hired' ? 'text-green-700 dark:text-green-400' : 'text-red-700 dark:text-red-400' }}">
                                        {{ $step['id'] === 'hired' ? '🎉 ¡Felicidades! Has sido contratado.' : '😔 Gracias por participar, sigue postulando a nuevas oportunidades.' }}
                                    </p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- 🔙 Botón volver abajo --}}
        <div class="mt-10 text-center">
            <a href="{{ route('user.applications.index') }}"
               class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-sky-600 to-blue-600 hover:from-sky-700 hover:to-blue-700 text-white font-semibold shadow-md transition">
                ← Volver a mis postulaciones
            </a>
        </div>
    </div>
</x-app-layout>
