<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
            Crear Vacante
        </h2>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto">
        <form action="{{ route('admin.jobs.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            @csrf

            {{-- Título --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Título</label>
                <input 
                    type="text" 
                    name="title" 
                    value="{{ old('title') }}" 
                    class="mt-1 w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-gray-100" 
                    required
                >
                @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Descripción --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descripción</label>
                <textarea 
                    name="description" 
                    rows="5" 
                    class="mt-1 w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-gray-100" 
                    required>{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Imagen --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Imagen de la vacante (opcional)</label>
                <input 
                    type="file" 
                    name="image" 
                    accept="image/*" 
                    class="mt-1 w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-gray-100"
                >
                @error('image')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Botón --}}
            <div class="flex justify-end">
                <button 
                    type="submit" 
                    class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg shadow transition"
                >
                    Guardar
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
