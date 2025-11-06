<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl sm:text-2xl font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-md bg-blue-600/10 text-blue-700 dark:text-blue-400">⚖️</span>
                Términos y Condiciones
            </h2>

            <a href="{{ url('/') }}"
               class="hidden sm:inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-white text-sm font-medium shadow hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400">
               ⟵ Inicio
            </a>
        </div>
    </x-slot>

    <style>
        /* Estilo “sticker” con sombra dura en azul */
        .blue_border { box-shadow: 4px 4px 1px rgb(37, 99, 235); } /* tailwind blue-600 */
        .black_border { box-shadow: 4px 4px 1px rgb(0, 0, 0); }
        .sticker-btn { transform: rotate(-8deg); }
        .smooth { transition: all .2s ease; }
    </style>

    <!-- Fondo agradable: azul muy suave (claro) y gris profundo (oscuro) -->
    <div class="min-h-[calc(100vh-6rem)] bg-blue-50/70 dark:bg-gray-950">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 py-8">

            <!-- Card principal con borde negro + sombra azul dura -->
            <div class="relative">
                <button
                    class="absolute py-1 px-3 -left-3 -top-3 sticker-btn border border-black black_border bg-blue-600 text-white text-xs sm:text-sm font-bold">
                    INFO
                </button>

                <div class="blue_border bg-white dark:bg-gray-900 border border-black rounded-xl p-6 sm:p-8">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">
                        Bienvenido a <strong>wr consultorias</strong>.
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-500 mb-6">
                        Última actualización: {{ now()->format('d/m/Y') }}
                    </p>

                    <div class="space-y-6 text-gray-900 dark:text-gray-100">
                        <section id="uso">
                            <h3 class="text-lg font-semibold">1) Uso de la plataforma</h3>
                            <p class="mt-2 leading-relaxed">
                                Esta plataforma facilita el proceso de reclutamiento entre candidatos y administradores.
                                Debes utilizarla de forma lícita y respetuosa con las políticas internas y derechos de terceros.
                            </p>
                        </section>

                        <hr class="border-black/30">

                        <section id="datos">
                            <h3 class="text-lg font-semibold">2) Datos personales</h3>
                            <div class="relative mt-3">
                                <span class="absolute -z-10 w-full h-full inset-1 bg-blue-500 rounded-lg"></span>
                                <div class="p-4 sm:p-5 bg-white dark:bg-gray-900 border border-black rounded-lg blue_border">
                                    <p class="leading-relaxed">
                                        Los CVs y datos ingresados se usarán solo para fines de reclutamiento.
                                        Implementamos medidas de seguridad y retención conforme a la política de privacidad.
                                    </p>
                                </div>
                            </div>
                        </section>

                        <hr class="border-black/30">

                        <section id="responsabilidad">
                            <h3 class="text-lg font-semibold">3) Responsabilidad</h3>
                            <div class="relative mt-3">
                                <button
                                    class="absolute py-1 px-3 -left-3 -top-3 sticker-btn border border-black black_border bg-blue-600 text-white text-xs sm:text-sm font-bold">
                                    WARNING!
                                </button>
                                <div class="p-4 sm:p-5 bg-white dark:bg-gray-900 border border-black rounded-lg blue_border">
                                    <p class="leading-relaxed">
                                        No garantizamos contratación ni resultados específicos. El uso indebido puede conllevar
                                        suspensión o cierre de cuenta.
                                    </p>
                                </div>
                            </div>
                        </section>

                        <hr class="border-black/30">

                        <section id="modificaciones">
                            <h3 class="text-lg font-semibold">4) Modificaciones</h3>
                            <p class="mt-2 leading-relaxed">
                                Podemos actualizar estos términos en cualquier momento. Revisa periódicamente para estar al tanto
                                de cambios importantes; procuraremos notificarlos por los medios habituales.
                            </p>
                        </section>

                        <div class="pt-2">
                            <a href="{{ url('/') }}"
                               class="inline-flex items-center gap-2 rounded-md border border-black bg-white dark:bg-transparent px-4 py-2 text-blue-700 dark:text-blue-400 black_border smooth hover:-translate-y-0.5">
                                ⟵ Volver al inicio
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botón flotante “arriba” estilo sticker -->
            <button id="toTopBtn"
                    class="fixed bottom-5 right-5 z-40 hidden rounded-md border border-black black_border bg-blue-600 hover:bg-blue-500 text-white w-10 h-10 grid place-items-center focus:outline-none"
                    aria-label="Volver arriba" title="Volver arriba">
                ⬆
            </button>
        </div>
    </div>

    <script>
        // Mostrar/ocultar botón "arriba"
        const toTopBtn = document.getElementById('toTopBtn');
        const toggleTopBtn = () => {
            if (window.scrollY > 280) toTopBtn.classList.remove('hidden');
            else toTopBtn.classList.add('hidden');
        };
        window.addEventListener('scroll', toggleTopBtn);
        toTopBtn?.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    </script>
</x-app-layout>
