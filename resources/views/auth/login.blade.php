<x-guest-layout>
    <div class="min-h-screen bg-gray-100 text-gray-900 flex items-center justify-center py-6 px-4 sm:px-6 lg:px-8">
         <div class="max-w-screen-xl w-full sm:mx-6 bg-white shadow-2xl rounded-2xl border border-gray-200 flex flex-col lg:flex-row justify-center relative">
            
            <!-- 🔙 Volver al inicio -->
            <a href="{{ url('/') }}"
               class="absolute top-5 left-5 flex items-center text-sm text-gray-500 hover:text-indigo-600 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                <span>Volver al inicio</span>
            </a>

            <!-- Formulario -->
            <div class="lg:w-1/2 xl:w-5/12 p-6 sm:p-12">
                <div class="text-center">
                    <h1 class="mt-6 text-2xl xl:text-3xl font-extrabold">
                        Iniciar Sesión
                    </h1>
                </div>

                <!-- Session Status -->
                <x-auth-session-status class="mt-4 mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="mt-8">
                    @csrf

                    <div class="mx-auto max-w-xs">
                        <!-- Email -->
                        <x-input-label for="email" :value="__('Correo electrónico')" />
                        <x-text-input
                            id="email"
                            class="block mt-1 w-full bg-gray-100 border border-gray-200 text-sm"
                            type="email"
                            name="email"
                            :value="old('email')"
                            required
                            autofocus
                            autocomplete="username"
                        />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />

                        <!-- Password -->
                        <div class="mt-5">
                            <x-input-label for="password" :value="__('Contraseña')" />
                            <x-text-input
                                id="password"
                                class="block mt-1 w-full bg-gray-100 border border-gray-200 text-sm"
                                type="password"
                                name="password"
                                required
                                autocomplete="current-password"
                            />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Remember Me -->
                        <div class="block mt-4">
                            <label for="remember_me" class="inline-flex items-center">
                                <input id="remember_me" type="checkbox"
                                    class="rounded border-gray-500 text-black shadow-sm focus:ring-gray-800"
                                    name="remember">
                                <span class="ms-2 text-sm text-gray-600">{{ __('Recuérdame') }}</span>
                            </label>
                        </div>

                        <!-- Botón normal -->
                        <button
                            class="mt-5 tracking-wide font-semibold bg-gray-800 text-white w-full py-3 rounded-lg hover:bg-gray-700 transition-all duration-300 ease-in-out flex items-center justify-center">
                            <svg class="w-6 h-6 -ml-2" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                                <circle cx="8.5" cy="7" r="4" />
                                <path d="M20 8v6M23 11h-6" />
                            </svg>
                            <span class="ml-3">Iniciar Sesión</span>
                        </button>

                        <!-- Link olvidaste tu contraseña -->
                        @if (Route::has('password.request'))
                            <div class="mt-4 text-center">
                                <a class="text-sm text-gray-600 hover:text-gray-900 underline"
                                    href="{{ route('password.request') }}">
                                    ¿Olvidaste tu contraseña?
                                </a>
                            </div>
                        @endif

                        <!-- Divider -->
                        <div class="flex items-center my-4">
                            <hr class="flex-grow border-gray-300">
                            <span class="px-2 text-gray-500 text-xs">o</span>
                            <hr class="flex-grow border-gray-300">
                        </div>

                        <!-- Botón Google con loader -->
                        <button id="btn-google" type="button"
                            onclick="startGoogleLogin()"
                            class="w-full flex items-center justify-center gap-2 py-2.5 px-4 border border-gray-300 rounded-lg bg-white text-gray-700 font-medium text-sm shadow-sm hover:bg-gray-50 transition-all duration-200">
                            <svg id="google-icon" class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48">
                                <path fill="#FFC107"
                                    d="M43.6 20.1H42V20H24v8h11.3c-1.4 4-5.3 6.9-9.3 6.9-6.6 
                                    0-12-5.4-12-12s5.4-12 12-12c3.1 0 5.8 1.2 7.9 
                                    3l5.7-5.7C34 5.1 29.3 3 24 
                                    3 12.9 3 4 11.9 4 23s8.9 
                                    20 20 20c11 0 20-8.9 
                                    20-20 0-1.4-.1-2.7-.4-3.9z"/>
                            </svg>
                            <span id="google-text">Continuar con Google</span>
                                                        <!-- Loader -->
                            <svg id="google-loader" class="hidden w-5 h-5 animate-spin text-gray-700"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 100 16v-4l-3 3 3 3v-4a8 8 0 01-8-8z"></path>
                            </svg>
                        </button>

                        <!-- 🧭 Enlace elegante para registrarse -->
                        <div class="mt-6 text-center text-sm">
                            <span class="text-gray-600">¿No tienes una cuenta?</span>
                            <a href="{{ route('register') }}" 
                               class="font-semibold text-indigo-600 hover:text-indigo-500 transition-colors duration-200 ml-1">
                                Regístrate aquí
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Imagen decorativa -->
            <div class="flex-1 bg-no-repeat bg-cover bg-center hidden lg:flex rounded-r-2xl overflow-hidden relative"
                style="background-image: url('{{ asset('images/auth/Login.jpg') }}'); box-shadow: 4px 0 15px -4px rgba(0, 0, 0, 0.2), 0 4px 15px -4px rgba(0, 0, 0, 0.1);">
                <div class="absolute inset-0 bg-black bg-opacity-20"></div> 
            </div>
        </div>
    </div>

    <!-- Script loader Google -->
    <script>
        function startGoogleLogin() {
            const btn = document.getElementById('btn-google');
            const icon = document.getElementById('google-icon');
            const text = document.getElementById('google-text');
            const loader = document.getElementById('google-loader');

            btn.disabled = true;
            icon.classList.add('hidden');
            text.textContent = "Conectando...";
            loader.classList.remove('hidden');
        }
    </script>
</x-guest-layout>
