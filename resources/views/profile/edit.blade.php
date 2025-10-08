<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100 leading-tight">
            👤 Perfil de usuario
        </h2>
    </x-slot>

    <div class="py-12 bg-gradient-to-b from-sky-50 via-white to-slate-50 dark:from-gray-900 dark:via-gray-950 dark:to-black min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- ✅ Mensajes flash --}}
            @if (session('status'))
                <div class="p-4 mb-4 flex items-center gap-2 rounded-xl border border-green-300 bg-green-50 dark:bg-green-900/20 dark:border-green-800 text-green-800 dark:text-green-300 shadow-sm">
                    <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            {{-- =========================
                 Actualizar información
            ========================== --}}
            <div class="p-6 bg-white/90 dark:bg-gray-900/80 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm backdrop-blur">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
                    📝 Actualizar información
                </h3>

                @if ($user->provider === 'google')
                    <div class="text-sm text-gray-600 dark:text-gray-400 space-y-2">
                        <p>Tu cuenta está vinculada con Google. No puedes editar tu nombre ni correo desde aquí.</p>
                        <div class="mt-4 text-gray-800 dark:text-gray-200">
                            <p><strong>Nombre:</strong> {{ $user->name }}</p>
                            <p><strong>Email:</strong> {{ $user->email }}</p>
                        </div>
                    </div>
                @else
                    <form method="POST" action="{{ route('profile.update.info') }}" class="space-y-5">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                   class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:ring-sky-500 focus:border-sky-500" required>
                            @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                   class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:ring-sky-500 focus:border-sky-500" required>
                            @error('email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <button type="submit"
                                class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-sky-600 to-blue-600 hover:from-sky-700 hover:to-blue-700 text-white font-semibold shadow-sm transition">
                            Guardar cambios
                        </button>
                    </form>
                @endif
            </div>

            {{-- =========================
                 Cambiar contraseña
            ========================== --}}
            <div class="p-6 bg-white/90 dark:bg-gray-900/80 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm backdrop-blur">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
                    🔒 Cambiar contraseña
                </h3>

                @if ($user->provider === 'google')
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Tu cuenta está vinculada con Google. No puedes cambiar tu contraseña desde aquí.
                    </p>
                @else
                    <form method="POST" action="{{ route('profile.update.password') }}" class="space-y-5">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nueva contraseña</label>
                            <input type="password" name="password"
                                   class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                            @error('password') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirmar contraseña</label>
                            <input type="password" name="password_confirmation"
                                   class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                        </div>

                        <button type="submit"
                                class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white font-semibold shadow-sm transition">
                            Actualizar contraseña
                        </button>
                    </form>
                @endif
            </div>

            {{-- =========================
                 Eliminar cuenta
            ========================== --}}
            <div class="p-6 bg-white/90 dark:bg-gray-900/80 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm backdrop-blur">
                <h3 class="text-lg font-semibold text-red-600 dark:text-red-400 mb-4 flex items-center gap-2">
                    ⚠️ Eliminar cuenta
                </h3>

                <form method="POST" action="{{ route('profile.destroy') }}" class="space-y-5">
                    @csrf
                    @method('DELETE')

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirma tu contraseña</label>
                        <input type="password" name="password"
                               class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:ring-red-500 focus:border-red-500" required>
                        @error('password') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit"
                            onclick="return confirm('⚠️ ¿Seguro que deseas eliminar tu cuenta? Esta acción no se puede deshacer.')"
                            class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white font-semibold shadow-sm transition">
                        Eliminar cuenta
                    </button>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
 