<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl sm:text-2xl text-gray-800 dark:text-gray-100 leading-tight">
                    Panel de Administrador
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Bienvenido, <span class="font-medium">{{ auth()->user()->name }}</span>. Gestiona vacantes y monitorea la actividad.
                </p>
            </div>

            <!-- Acciones rápidas (desktop) -->
            <div class="hidden sm:flex gap-2">
                <a href="{{ route('admin.jobs.index') }}"
                   class="inline-flex items-center gap-2 bg-sky-600 hover:bg-sky-500 text-white px-4 py-2 rounded-xl shadow-sm transition">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M3 6h18M3 12h18M3 18h18" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    Vacantes
                </a>
                <a href="{{ route('admin.reports.index') }}"
                   class="inline-flex items-center gap-2 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-100 px-4 py-2 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M3 3v18h18M7 15l3-3 4 4 5-6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Reportes
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Fondo profesional con gradiente y patrón de puntos (cubre 100vh) -->
    <section class="relative min-h-screen overflow-hidden
        pb-24 sm:pb-10 pt-6 sm:pt-10
        bg-gradient-to-b from-sky-50 via-white to-slate-50
        dark:from-gray-900 dark:via-gray-950 dark:to-black">

        <!-- overlay patrón -->
        <div class="pointer-events-none absolute inset-0 opacity-60 dark:opacity-40 -z-10">
            <div class="absolute inset-0
                bg-[radial-gradient(circle_at_1px_1px,#e2e8f0_1px,transparent_1px)]
                [background-size:24px_24px]
                dark:bg-[radial-gradient(circle_at_1px_1px,#111827_1px,transparent_1px)]">
            </div>
            <!-- halos sutiles -->
            <div class="absolute -top-24 -left-24 h-72 w-72 rounded-full blur-3xl
                        bg-sky-200/40 dark:bg-sky-900/20"></div>
            <div class="absolute -bottom-24 -right-24 h-72 w-72 rounded-full blur-3xl
                        bg-blue-200/40 dark:bg-blue-900/20"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Card bienvenida (sin botones) -->
            <div class="rounded-2xl border border-gray-200/70 dark:border-gray-800/70
                        bg-white/80 dark:bg-gray-900/70 backdrop-blur shadow-sm p-6">
                <p class="text-gray-700 dark:text-gray-300">
                    <span class="font-semibold">Bienvenido, Melvin Gomez.</span> Aquí puedes publicar y gestionar vacantes.
                </p>
            </div>

            <!-- Acciones rápidas (tarjetas) -->
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                <a href="{{ route('admin.jobs.index') }}"
                   class="group rounded-2xl border border-gray-200 dark:border-gray-800
                          bg-white/80 dark:bg-gray-900/70 backdrop-blur p-5
                          hover:bg-white dark:hover:bg-gray-900 transition shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-800 dark:text-gray-100">Vacantes publicadas</span>
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-sky-50 text-sky-600 dark:bg-sky-900/30 dark:text-sky-300">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 6h18M3 12h18M3 18h18" stroke-width="2" stroke-linecap="round"/></svg>
                        </span>
                    </div>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Administra, edita o cierra vacantes.</p>
                </a>

                <a href="{{ route('admin.reports.index') }}"
                   class="group rounded-2xl border border-gray-200 dark:border-gray-800
                          bg-white/80 dark:bg-gray-900/70 backdrop-blur p-5
                          hover:bg-white dark:hover:bg-gray-900 transition shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-800 dark:text-gray-100">Reportes</span>
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-purple-50 text-purple-600 dark:bg-purple-900/30 dark:text-purple-300">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M4 19V5M12 19V9M20 19V13" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </span>
                    </div>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Métricas de postulaciones y embudo.</p>
                </a>

                <a href="{{ route('admin.jobs.create') }}"
                   class="group rounded-2xl border border-gray-200 dark:border-gray-800
                          bg-white/80 dark:bg-gray-900/70 backdrop-blur p-5
                          hover:bg-white dark:hover:bg-gray-900 transition shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-800 dark:text-gray-100">Nueva vacante</span>
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-300">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 5v14M5 12h14" stroke-width="2" stroke-linecap="round"/></svg>
                        </span>
                    </div>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Publica una nueva oportunidad.</p>
                </a>
            </div>

            <!-- CTA móvil fijo -->
            <div class="sm:hidden fixed bottom-4 inset-x-4 z-40">
                <div class="rounded-2xl shadow-lg border border-gray-200 dark:border-gray-800 bg-white/90 dark:bg-gray-900/90 backdrop-blur p-3 flex items-center gap-2">
                    <a href="{{ route('admin.jobs.index') }}"
                       class="flex-1 inline-flex items-center justify-center gap-2 bg-sky-600 hover:bg-sky-500 text-white px-4 py-2 rounded-xl transition">
                        Vacantes
                    </a>
                    <a href="{{ route('admin.reports.index') }}"
                       class="flex-1 inline-flex items-center justify-center gap-2 border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-100 px-4 py-2 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        Reportes
                    </a>
                </div>
            </div>

        </div>
    </section>
</x-app-layout>
