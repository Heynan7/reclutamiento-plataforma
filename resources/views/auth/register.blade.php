<x-guest-layout>
    <div class="min-h-screen bg-gray-100 text-gray-900 flex items-center justify-center py-6 px-4 sm:px-6 lg:px-8">
        <div class="max-w-screen-xl w-full sm:mx-6 bg-white shadow-2xl rounded-2xl border border-gray-200 flex flex-col lg:flex-row justify-center">
            
            <!-- Formulario -->
            <div class="lg:w-1/2 xl:w-5/12 p-6 sm:p-12">
                <div class="text-center">
                    <h1 class="mt-6 text-2xl xl:text-3xl font-extrabold">Crear cuenta</h1>
                    <p class="mt-2 text-sm text-gray-600">
                        Después de registrarte, te enviaremos un correo para <span class="font-semibold">verificar tu dirección</span>.  
                        Revisa tu bandeja de entrada 📩.
                    </p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="mt-8">
                    @csrf

                    <div class="mx-auto max-w-xs space-y-4">
                        <!-- Nombre -->
                        <div>
                            <x-input-label for="name" :value="__('Nombre completo')" />
                            <x-text-input id="name" class="block w-full bg-gray-100 border border-gray-200 text-sm" 
                                type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Correo electrónico -->
                        <div>
                            <x-input-label for="email" :value="__('Correo electrónico')" />
                            <x-text-input id="email" class="block w-full bg-gray-100 border border-gray-200 text-sm" 
                                type="email" name="email" :value="old('email')" required autocomplete="email" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Teléfono -->
<div>
    <x-input-label for="phone" :value="__('Número de teléfono')" />

    <div class="flex items-center">
        <span class="px-3 py-2 bg-gray-200 border border-r-0 border-gray-300 text-gray-700 text-sm rounded-l">
            +502
        </span>
            <x-text-input id="phone" 
                class="block w-full bg-gray-100 border border-gray-300 text-sm rounded-r" 
                type="tel" name="phone" value="{{ old('phone') ? str_replace('+502', '', old('phone')) : '' }}"
                required pattern="^[0-9]{8}$" 
                placeholder="12345678" />
    </div>

    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
    <p class="text-xs text-gray-500 mt-1">Ingresa solo los 8 dígitos de tu número.</p>
</div>

                        <!-- Contraseña -->
                        <div>
                            <x-input-label for="password" :value="__('Contraseña')" />
                            <x-text-input id="password" class="block w-full bg-gray-100 border border-gray-200 text-sm" 
                                type="password" name="password" required autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>


                        <!-- Confirmación de contraseña -->
                        <div>
                            <x-input-label for="password_confirmation" :value="__('Confirmar Contraseña')" />
                            <x-text-input id="password_confirmation" class="block w-full bg-gray-100 border border-gray-200 text-sm" 
                                type="password" name="password_confirmation" required autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>

                        <!-- Botón -->
                        <button class="mt-4 tracking-wide font-semibold bg-gray-800 text-white w-full py-4 rounded-lg hover:bg-gray-700 transition-all duration-300 ease-in-out flex items-center justify-center">
                            <svg class="w-6 h-6 -ml-2" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                                <circle cx="8.5" cy="7" r="4" />
                                <path d="M20 8v6M23 11h-6" />
                            </svg>
                            <span class="ml-3">Registrarse</span>
                        </button>

                        <!-- Ya registrado -->
                        <div class="mt-4 text-center">
                            <a class="text-sm text-gray-600 hover:text-gray-900 underline" href="{{ route('login') }}">
                                ¿Ya tienes una cuenta? Inicia sesión
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Imagen decorativa -->
            <div class="flex-1 bg-no-repeat bg-cover bg-center hidden lg:flex rounded-r-2xl overflow-hidden relative"
                style="background-image: url('{{ asset('images/auth/Registro.jpg') }}'); box-shadow: 4px 0 15px -4px rgba(0, 0, 0, 0.2), 0 4px 15px -4px rgba(0, 0, 0, 0.1);">
                <div class="absolute inset-0 bg-black bg-opacity-20"></div>
            </div>
        </div>
    </div>
</x-guest-layout>
