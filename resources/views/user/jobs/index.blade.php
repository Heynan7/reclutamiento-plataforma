<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            Vacantes disponibles
        </h2>
    </x-slot>

    <div class="py-6 px-4">

        {{-- Mensajes de éxito o error --}}
        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif

        @if($jobs->isEmpty())
            {{-- Estado vacío --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl p-10 text-center shadow">
                <div class="text-3xl mb-2">🙌</div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">No hay vacantes disponibles para ti por ahora</h3>
                <p class="text-gray-600 dark:text-gray-300 mt-2">
                    Vuelve más tarde: cuando nuevas vacantes se abran, las verás aquí.
                </p>
            </div>
        @else
            {{-- Grid de vacantes --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($jobs as $job)
                    <article class="bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition overflow-hidden border border-gray-100 dark:border-gray-700">
                        {{-- Imagen de la vacante --}}
                        @if($job->image)
                            <div class="w-full h-56 bg-gray-50 dark:bg-gray-700 overflow-hidden">
                                <img
                                    src="{{ route('user.jobs.image', $job->id) }}"
                                    alt="Imagen de la vacante {{ $job->title }}"
                                    class="w-full h-full object-contain p-2 transition-transform duration-300 hover:scale-105"
                                    loading="lazy"
                                >
                            </div>
                        @else
                            <div class="w-full h-56 bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-400">
                                <span class="text-sm">Sin imagen</span>
                            </div>
                        @endif

                        {{-- Contenido --}}
                        <div class="p-5">
                            <h3 class="text-lg font-bold text-indigo-700 dark:text-indigo-400 line-clamp-1">
                                {{ $job->title }}
                            </h3>

                            <p class="text-gray-600 dark:text-gray-300 mt-2">
                                {{ \Illuminate\Support\Str::limit($job->description, 120) }}
                            </p>

                            {{-- Botón de ver más --}}
                            <a
                                href="{{ route('user.jobs.show', $job->id) }}"
                                class="mt-4 inline-block px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg shadow hover:bg-indigo-700 transition"
                                aria-label="Ver detalles de {{ $job->title }}"
                            >
                                Ver detalles
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- Paginación (solo si $jobs es un paginator) --}}
            @if(method_exists($jobs, 'links'))
                <div class="mt-6">
                    {{ $jobs->links() }}
                </div>
            @endif
        @endif
    </div>
</x-app-layout>
