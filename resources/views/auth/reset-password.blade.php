<x-guest-layout>
    <div class="min-h-screen bg-gray-100 text-gray-900 flex items-center justify-center py-6 px-4 sm:px-6 lg:px-8">
        <div class="max-w-screen-xl w-full sm:mx-6 bg-white shadow-2xl rounded-2xl border border-gray-200 flex flex-col lg:flex-row justify-center">

            <!-- Formulario -->
            <div class="lg:w-1/2 xl:w-5/12 p-6 sm:p-12">
                <div class="text-center">
                  
                        class="w-32 mx-auto" alt="Logo" />
                    <h1 class="mt-6 text-2xl xl:text-3xl font-extrabold">
                        Restablecer contraseña
                    </h1>
                    <p class="mt-2 text-sm text-gray-600">
                        Ingresa tu correo y una nueva contraseña para recuperar el acceso a tu cuenta.
                    </p>
                </div>

                <form method="POST" action="{{ route('password.store') }}" class="mt-8">
                    @csrf

                    <!-- Token de recuperación -->
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <div class="mx-auto max-w-xs space-y-4">
                        <!-- Email -->
                        <div>
                            <x-input-label for="email" :value="__('Correo electrónico')" />
                            <x-text-input
                                id="email"
                                class="block w-full bg-gray-100 border border-gray-200 text-sm"
                                type="email"
                                name="email"
                                :value="old('email', $request->email)"
                                required
                                autofocus
                                autocomplete="username"
                            />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Contraseña -->
                        <div>
                            <x-input-label for="password" :value="__('Nueva contraseña')" />
                            <x-text-input
                                id="password"
                                class="block w-full bg-gray-100 border border-gray-200 text-sm"
                                type="password"
                                name="password"
                                required
                                autocomplete="new-password"
                            />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Confirmar contraseña -->
                        <div>
                            <x-input-label for="password_confirmation" :value="__('Confirmar contraseña')" />
                            <x-text-input
                                id="password_confirmation"
                                class="block w-full bg-gray-100 border border-gray-200 text-sm"
                                type="password"
                                name="password_confirmation"
                                required
                                autocomplete="new-password"
                            />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>

                        <!-- Botón -->
                        <button
                            class="mt-6 tracking-wide font-semibold bg-gray-800 text-white w-full py-3 rounded-lg hover:bg-gray-700 transition-all duration-300 ease-in-out flex items-center justify-center">
                            <span class="ml-2">Restablecer contraseña</span>
                        </button>

                        <!-- Volver a login -->
                        <div class="mt-4 text-center">
                            <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">
                                Volver al inicio de sesión
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Imagen decorativa -->
            <div class="flex-1 bg-no-repeat bg-cover bg-center hidden lg:flex rounded-r-2xl overflow-hidden relative"
                style="background-image: url('{{ asset('images/auth/ResstablecerContraseña.jpg') }}'); box-shadow: 4px 0 15px -4px rgba(0, 0, 0, 0.2), 0 4px 15px -4px rgba(0, 0, 0, 0.1);">
                <div class="absolute inset-0 bg-black bg-opacity-20"></div>
            </div>
        </div>
    </div>
</x-guest-layout>
