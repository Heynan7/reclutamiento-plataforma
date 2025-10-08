<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                ✏️ <span>Editar Vacante</span>
            </h2>

            <a href="{{ route('admin.jobs.index') }}"
               class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl font-medium
                      bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200
                      shadow-sm hover:shadow-md hover:bg-gray-200 dark:hover:bg-gray-700
                      transition-all duration-200 w-full sm:w-auto text-center">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-10 px-4">
        <div class="max-w-3xl mx-auto bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 
                    rounded-2xl shadow-xl p-6 sm:p-8 transition-all duration-300">

            <form action="{{ route('admin.jobs.update', $job) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- 🔹 Título --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Título de la vacante</label>
                    <input type="text" name="title" value="{{ old('title', $job->title) }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-700 
                                  bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 
                                  focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition">
                    @error('title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 🔹 Descripción --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Descripción</label>
                    <textarea name="description" rows="5"
                              class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-700 
                                     bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 
                                     focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition">{{ old('description', $job->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 🔹 Imagen actual --}}
                @if($job->image)
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Imagen actual</label>
                        <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm bg-gray-50 dark:bg-gray-800">
                            <img src="{{ route('admin.jobs.image', $job->id) }}" 
                                 alt="Imagen de {{ $job->title }}"
                                 class="w-full max-h-72 object-contain hover:scale-105 transition-transform duration-300">
                        </div>
                    </div>
                @endif

                {{-- 🔹 Nueva imagen con previsualización --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        Cambiar imagen (opcional)
                    </label>
                    <input id="newImageInput" type="file" name="image" accept="image/*"
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-700 
                                  bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100
                                  focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Formatos permitidos: JPG, JPEG, PNG. Máx: 10 MB
                    </p>
                    @error('image')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror

                    {{-- Previsualización --}}
                    <div id="previewContainer" class="hidden mt-4 border border-gray-200 dark:border-gray-700 
                                                     rounded-xl p-3 bg-gray-50 dark:bg-gray-800 shadow-sm">
                        <p class="text-sm text-gray-600 dark:text-gray-300 mb-2 font-medium">
                            Previsualización:
                        </p>
                        <img id="previewImage" class="max-h-64 w-full object-contain rounded-lg shadow-md" alt="Previsualización">
                    </div>
                </div>

                {{-- 🔘 Botones --}}
                <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('admin.jobs.index') }}"
                       class="w-full sm:w-auto px-5 py-2 rounded-xl text-sm font-medium 
                              bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 
                              hover:bg-gray-200 dark:hover:bg-gray-700 shadow-sm transition text-center">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="w-full sm:w-auto px-6 py-2 rounded-xl text-sm font-medium text-white 
                                   bg-blue-600 hover:bg-blue-700 shadow-md hover:shadow-lg transform hover:scale-[1.02]
                                   transition-all duration-200">
                        💾 Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- 🖼️ Script para previsualizar la nueva imagen --}}
    <script>
        const input = document.getElementById('newImageInput');
        const previewContainer = document.getElementById('previewContainer');
        const previewImage = document.getElementById('previewImage');

        input?.addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (!file) {
                previewContainer.classList.add('hidden');
                previewImage.src = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = (e) => {
                previewImage.src = e.target.result;
                previewContainer.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        });
    </script>
</x-app-layout>
