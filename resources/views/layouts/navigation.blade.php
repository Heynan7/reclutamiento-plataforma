<nav x-data="{ open: false }"
     class="relative bg-white/90 dark:bg-gray-900/90 backdrop-blur border-b border-gray-200 dark:border-gray-800 shadow-sm transition">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">

            {{-- 🔷 LOGO / Enlaces principales --}}
            <div class="flex items-center gap-6">
                <a href="{{ url('/') }}"
                   class="text-lg font-black text-gray-800 dark:text-gray-100 hover:text-sky-600 dark:hover:text-sky-400 transition">
                    WR Consultorías<span class="text-sky-600">.</span>
                </a>

                {{-- Enlaces principales (desktop) --}}
                <div class="hidden sm:flex items-center gap-6 text-sm font-medium">
                    @auth
                        @if(Auth::user()->role === 'admin')
                            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                                Panel de Admin
                            </x-nav-link>
                        @else
                            <x-nav-link :href="route('user.dashboard')" :active="request()->routeIs('user.dashboard')">
                                Panel del Aspirante
                            </x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>

            {{-- ⚙️ Dropdown de usuario (desktop) --}}
            @auth
                <div class="hidden sm:flex items-center gap-3">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button
                                class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                                {{-- Avatar --}}
                                @if(Auth::user()->avatar)
                                    <img src="{{ Auth::user()->avatar }}" alt="Avatar"
                                         class="h-8 w-8 rounded-full border border-gray-300 dark:border-gray-700 object-cover">
                                @else
                                    <div
                                        class="h-8 w-8 rounded-full bg-sky-100 dark:bg-sky-800 flex items-center justify-center font-semibold text-sky-700 dark:text-sky-300">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </div>
                                @endif
                                <span>{{ Auth::user()->name }}</span>
                                <svg class="h-4 w-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                🧑 Perfil
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                                 onclick="event.preventDefault(); this.closest('form').submit();">
                                    🚪 Cerrar sesión
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            @endauth

            {{-- ☰ Menú hamburguesa (móvil) --}}
            <div class="sm:hidden">
                <button @click="open = !open"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open}"
                              class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{'hidden': !open, 'inline-flex': open}"
                              class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- 📱 Menú responsive desplegable con animación --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform -translate-y-2"
         class="sm:hidden border-t border-gray-200 dark:border-gray-800 bg-white/95 dark:bg-gray-900/95 backdrop-blur-md shadow-md">
        <div class="py-4 px-4 space-y-3">
            @auth
                @if(Auth::user()->role === 'admin')
                    <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                        Panel de Admin
                    </x-responsive-nav-link>
                @else
                    <x-responsive-nav-link :href="route('user.dashboard')" :active="request()->routeIs('user.dashboard')">
                        Panel del Aspirante
                    </x-responsive-nav-link>
                @endif

                <hr class="border-gray-200 dark:border-gray-700">

                {{-- Perfil compacto --}}
                <div class="flex items-center gap-3">
                    @if(Auth::user()->avatar)
                        <img src="{{ Auth::user()->avatar }}" alt="Avatar"
                             class="h-10 w-10 rounded-full border border-gray-300 dark:border-gray-700 object-cover">
                    @else
                        <div class="h-10 w-10 rounded-full bg-sky-100 dark:bg-sky-800 flex items-center justify-center font-semibold text-sky-700 dark:text-sky-300">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <div class="font-medium text-base text-gray-800 dark:text-gray-100">{{ Auth::user()->name }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</div>
                    </div>
                </div>

                <x-responsive-nav-link :href="route('profile.edit')">
                    🧑 Perfil
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                                           onclick="event.preventDefault(); this.closest('form').submit();">
                        🚪 Cerrar sesión
                    </x-responsive-nav-link>
                </form>
            @endauth
        </div>
    </div>
</nav>
