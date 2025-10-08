<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl sm:text-2xl text-gray-800 dark:text-gray-100 leading-tight">
                    Panel del Aspirante
                </h2>

            </div>

            <!-- Acción rápida -->
            <div class="hidden sm:flex gap-2">
                <a href="{{ route('user.jobs.index') }}"
                   class="inline-flex items-center gap-2 bg-sky-600 hover:bg-sky-500 text-white px-4 py-2 rounded-xl shadow-sm transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M3 6h18M3 12h18M3 18h18" stroke-linecap="round"/>
                    </svg>
                    Vacantes
                </a>
                <a href="{{ route('user.applications.index') }}"
                   class="inline-flex items-center gap-2 border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900
                          text-gray-800 dark:text-gray-100 px-4 py-2 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Mis postulaciones
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Fondo con gradiente y patrón -->
    <section class="relative min-h-screen overflow-hidden
        pb-24 sm:pb-10 pt-6 sm:pt-10
        bg-gradient-to-b from-blue-50 via-white to-slate-50
        dark:from-gray-900 dark:via-gray-950 dark:to-black">

        <!-- Patrón decorativo -->
        <div class="pointer-events-none absolute inset-0 opacity-60 dark:opacity-40 -z-10">
            <div class="absolute inset-0
                bg-[radial-gradient(circle_at_1px_1px,#e2e8f0_1px,transparent_1px)]
                [background-size:24px_24px]
                dark:bg-[radial-gradient(circle_at_1px_1px,#111827_1px,transparent_1px)]">
            </div>
            <div class="absolute -top-24 -left-24 h-72 w-72 rounded-full blur-3xl
                        bg-sky-200/40 dark:bg-sky-900/20"></div>
            <div class="absolute -bottom-24 -right-24 h-72 w-72 rounded-full blur-3xl
                        bg-blue-200/40 dark:bg-blue-900/20"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- 💫 Bienvenida animada -->
            <div class="rounded-3xl border border-gray-200/70 dark:border-gray-800/70
                        bg-gradient-to-r from-sky-100 via-blue-50 to-indigo-100
                        dark:from-sky-900/30 dark:via-blue-900/20 dark:to-indigo-900/30
                        backdrop-blur-md shadow-md p-6 sm:p-8 flex flex-col sm:flex-row items-center justify-between gap-6 transition">
                
                <!-- Texto -->
                <div class="text-center sm:text-left">
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-gray-800 dark:text-gray-100">
                        🎉 ¡Bienvenido de nuevo, <span class="text-transparent bg-clip-text bg-gradient-to-r from-sky-600 to-blue-600">{{ auth()->user()->name }}</span>!
                    </h3>
                    <p class="mt-2 text-gray-700 dark:text-gray-300 max-w-lg">
                        Nos alegra verte de nuevo 💼. Explora nuevas oportunidades laborales y revisa el estado de tus postulaciones fácilmente.
                    </p>
                </div>

                <!-- Imagen / ícono decorativo -->
                <div class="w-24 sm:w-28 flex-shrink-0">
                    <img src="https://cdn-icons-png.flaticon.com/512/9068/9068852.png" alt="Welcome"
                         class="w-full drop-shadow-lg animate-float">
                </div>
            </div>

            <!-- Tarjetas de acciones -->
            <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <a href="{{ route('user.jobs.index') }}"
                   class="group rounded-2xl border border-gray-200 dark:border-gray-800
                          bg-white/80 dark:bg-gray-900/70 backdrop-blur p-5
                          hover:bg-white dark:hover:bg-gray-900 transition shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-800 dark:text-gray-100">Vacantes disponibles</span>
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-sky-50 text-sky-600 dark:bg-sky-900/30 dark:text-sky-300">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M3 6h18M3 12h18M3 18h18" stroke-linecap="round"/>
                            </svg>
                        </span>
                    </div>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Explora las vacantes activas y aplica fácilmente.</p>
                </a>

                <a href="{{ route('user.applications.index') }}"
                   class="group rounded-2xl border border-gray-200 dark:border-gray-800
                          bg-white/80 dark:bg-gray-900/70 backdrop-blur p-5
                          hover:bg-white dark:hover:bg-gray-900 transition shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-800 dark:text-gray-100">Mis postulaciones</span>
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-300">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                    </div>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Consulta el estado de tus aplicaciones recientes.</p>
                </a>
            </div>

            {{-- CTA móvil fijo --}}
            <div class="sm:hidden fixed bottom-4 inset-x-4 z-40">
                <div class="rounded-2xl shadow-lg border border-gray-200 dark:border-gray-800 bg-white/90 dark:bg-gray-900/90 backdrop-blur p-3 flex items-center gap-2">
                    <a href="{{ route('user.jobs.index') }}"
                       class="flex-1 inline-flex items-center justify-center gap-2 bg-sky-600 hover:bg-sky-500 text-white px-4 py-2 rounded-xl transition">
                        Vacantes
                    </a>
                    <a href="{{ route('user.applications.index') }}"
                       class="flex-1 inline-flex items-center justify-center gap-2 border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-100 px-4 py-2 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        Postulaciones
                    </a>
                </div>
            </div>

        </div>
    </section>

    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
    </style>
</x-app-layout>
