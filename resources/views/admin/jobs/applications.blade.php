<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-xl sm:text-2xl font-bold leading-tight text-gray-900 dark:text-gray-100">
                Postulantes a la vacante:
                <span class="text-sky-600 dark:text-sky-400">{{ $job->title }}</span>
            </h2>

            {{-- 🔙 Regresar a Gestión de Vacantes --}}
            <a href="{{ route('admin.jobs.index') }}"
               class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold
                      text-white bg-gradient-to-r from-sky-600 to-indigo-600 shadow-sm
                      hover:from-sky-500 hover:to-indigo-500 active:opacity-95 transition">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M15 18l-6-6 6-6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Gestión de Vacantes
            </a>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 bg-gradient-to-b from-sky-50 to-white dark:from-gray-900 dark:to-gray-950 min-h-[60vh]">
        @if($applications->count())
            <form id="bulkForm" method="POST" action="{{ route('admin.applications.bulkUpdateStatus') }}"
                  class="max-w-7xl mx-auto rounded-2xl border border-gray-200 dark:border-gray-800
                         bg-white/90 dark:bg-gray-900/90 shadow-sm backdrop-blur p-4 sm:p-6">
                @csrf
                @method('PATCH')

                {{-- 🧩 Tabla modular (sin cambios) --}}
                <x-applications.table :applications="$applications" />

                <div class="mt-5 flex items-center justify-end">
                    <button type="button" id="openModal"
                            class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold
                                   text-white bg-sky-600 hover:bg-sky-500 active:bg-sky-600
                                   shadow-sm transition focus:outline-none focus:ring-2 focus:ring-sky-400 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M12 4v16m8-8H4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Cambiar estado a seleccionados
                    </button>
                </div>
            </form>
        @else
            <div class="max-w-3xl mx-auto">
                <div class="rounded-2xl border border-dashed border-gray-300 dark:border-gray-700
                            bg-white/70 dark:bg-gray-900/70 p-8 text-center shadow-sm">
                    <p class="text-gray-600 dark:text-gray-300">
                        No hay postulantes para esta vacante aún.
                    </p>
                    <div class="mt-4">
                        <a href="{{ route('admin.jobs.index') }}"
                           class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold
                                  text-white bg-gradient-to-r from-sky-600 to-indigo-600 shadow-sm
                                  hover:from-sky-500 hover:to-indigo-500 transition">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M15 18l-6-6 6-6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Volver a Gestión de Vacantes
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- 🧩 Modales (sin cambios) --}}
    <x-applications.modal-status />
    <x-applications.modal-analysis />
    <x-applications.modal-psychotest />

    {{-- 🧠 Script (sin cambios) --}}
    @vite('resources/js/applications.js')
</x-app-layout>
