<x-app-layout>  
    <div class="max-w-6xl mx-auto py-10 px-4 sm:px-6 lg:px-8">

        {{-- 🔙 Botón para volver al panel del usuario --}}
        <div class="flex justify-between items-center mb-8 flex-wrap gap-3">
            <a href="{{ route('user.dashboard') }}"
               class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-gradient-to-r from-sky-600 to-blue-600 hover:from-sky-700 hover:to-blue-700 text-white font-semibold shadow-md transition">
                ← Volver al panel de usuario
            </a>

            <h1 class="text-3xl font-extrabold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                📋 Mis postulaciones
            </h1>
        </div>

        @if($applications->count())
            {{-- 📄 Tabla responsiva --}}
            <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm bg-white/80 dark:bg-gray-900/70 backdrop-blur">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-900/60">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Vacante</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Fecha</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Estado</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Mi CV</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Acción</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-gray-700 dark:text-gray-300">
                        @foreach($applications as $app)
                            @php
                                $jobTitle = $app->job->title ?? $app->job_title_snapshot ?? 'Vacante';
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                <td class="px-4 py-3 font-semibold text-sky-700 dark:text-sky-400 whitespace-nowrap">
                                    {{ $jobTitle }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    {{ $app->created_at->format('d/m/Y H:i') }}
                                </td>

                                {{-- Estado con badge --}}
                                <td class="px-4 py-3">
                                    @switch($app->status)
                                        @case('submitted')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">📄 Recibido</span>
                                            @break
                                        @case('shortlisted')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">⭐ Preseleccionado</span>
                                            @break
                                        @case('interview_scheduled')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300">👔 Entrevista</span>
                                            @break
                                        @case('psychotest')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800 dark:bg-pink-900/30 dark:text-pink-300">🧠 Psicotest</span>
                                            @break
                                        @case('interview_deep')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300">🎤 Entrevista profunda</span>
                                            @break
                                        @case('socioeconomic_study')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300">🏠 Estudio socioeconómico</span>
                                            @break
                                        @case('hired')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">✅ Contratado</span>
                                            @break
                                        @case('rejected')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">❌ No contratado</span>
                                            @break
                                        @case('closed')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-800 dark:bg-gray-800 dark:text-gray-300">📌 Cerrado</span>
                                            @break
                                        @default
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-zinc-100 text-zinc-700 dark:bg-zinc-900/30 dark:text-zinc-300">-</span>
                                    @endswitch
                                </td>

                                {{-- CV --}}
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if($app->cv_file)
                                        <a href="{{ route('user.applications.downloadCv', $app->id) }}" target="_blank"
                                           class="text-sky-600 hover:underline text-xs font-medium">
                                            📄 Ver CV
                                        </a>
                                    @else
                                        <span class="text-gray-400 text-xs">No adjuntado</span>
                                    @endif
                                </td>

                                {{-- Acción --}}
                                <td class="px-4 py-3">
                                    <a href="{{ route('user.applications.show', $app) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-white bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 shadow-sm transition">
                                        Ver estado
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Footer --}}
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-3">
                💡 Consejo: puedes guardar el enlace de “Ver estado” para regresar más rápido.
            </p>

        @else
            <div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white/80 dark:bg-gray-900/70 p-6 text-center shadow-sm">
                <p class="text-gray-700 dark:text-gray-300">Aún no has enviado postulaciones.</p>
                <a href="{{ route('user.jobs.index') }}"
                   class="mt-4 inline-flex items-center gap-2 bg-sky-600 hover:bg-sky-500 text-white px-5 py-2 rounded-xl text-sm shadow-sm transition">
                    Ver vacantes disponibles
                </a>
            </div>
        @endif
    </div>
</x-app-layout>
