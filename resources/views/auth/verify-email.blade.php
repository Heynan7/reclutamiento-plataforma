<x-guest-layout>
    <div class="min-h-screen bg-gray-100 flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full bg-white shadow-2xl rounded-2xl border border-gray-200 p-8 text-center">
            
            <!-- Título -->
            <h1 class="text-2xl font-extrabold text-gray-800">Verifica tu correo electrónico</h1>

            <!-- Mensaje principal -->
            <p class="mt-4 text-sm text-gray-600 leading-relaxed">
                🎉 Gracias por registrarte en <span class="font-semibold">{{ config('app.name') }}</span>.  
                Antes de continuar, revisa tu correo y haz clic en el enlace de verificación.  
                <br><br>
                Si no recibiste el correo, puedes solicitar otro.
            </p>

            <!-- Mensaje de reenvío -->
            @if (session('status') == 'verification-link-sent')
                <div class="mt-4 bg-green-100 text-green-700 px-4 py-2 rounded-md text-sm font-medium">
                    📩 Se envió un nuevo enlace de verificación a tu correo.
                </div>
            @endif

            <!-- Botones -->
            <div class="mt-6 flex flex-col gap-3">
                <!-- Reenviar -->
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                        Reenviar correo de verificación
                    </button>
                </form>

                <!-- Cerrar sesión -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg transition duration-200">
                        Cerrar sesión
                    </button>
                </form>
            </div>

        </div>
    </div>
</x-guest-layout>
