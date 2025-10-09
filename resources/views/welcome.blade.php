<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>WR Consultorías - Tu puerta al empleo</title>
  @vite('resources/css/app.css')
</head>

<body class="bg-gray-50 text-gray-900 dark:bg-gray-950 dark:text-gray-100 transition-colors duration-300 overflow-x-hidden">

<!-- ===== Header / Navbar ===== -->
<header id="siteHeader" class="fixed top-0 inset-x-0 z-50 transition-all duration-300">
  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <div class="mt-3 rounded-2xl border border-white/20 dark:border-white/10 bg-white/70 dark:bg-gray-900/60 backdrop-blur-md shadow-sm">
      <div class="flex items-center justify-between px-4 py-3 md:px-6">
        <!-- Logo -->
        <a href="{{ url('/') }}" class="flex items-center gap-2 select-none">
          <span class="text-xl font-black tracking-tight">WR Consultorías<span class="text-sky-600">.</span></span>
        </a>

        <!-- Desktop Nav -->
        <nav class="hidden md:flex items-center gap-6">
          <a href="#about" class="relative font-medium text-gray-700 dark:text-gray-300 hover:text-sky-600 dark:hover:text-sky-400 transition-colors">
            <span>Sobre</span>
            <span class="absolute left-0 -bottom-1 h-0.5 w-0 bg-sky-600 transition-all duration-300 group-hover:w-full"></span>
          </a>
          <a href="#how" class="relative font-medium text-gray-700 dark:text-gray-300 hover:text-sky-600 dark:hover:text-sky-400 transition-colors">
            <span>Cómo postular</span>
            <span class="absolute left-0 -bottom-1 h-0.5 w-0 bg-sky-600 transition-all duration-300 group-hover:w-full"></span>
          </a>
          <a href="#contact" class="relative font-medium text-gray-700 dark:text-gray-300 hover:text-sky-600 dark:hover:text-sky-400 transition-colors">
            <span>Contacto</span>
            <span class="absolute left-0 -bottom-1 h-0.5 w-0 bg-sky-600 transition-all duration-300 group-hover:w-full"></span>
          </a>
        </nav>

        <!-- Right actions -->
        <div class="hidden md:flex items-center gap-3">
          <!-- Dark toggle -->
          <button id="darkToggle" class="p-2 rounded-lg border border-gray-200/70 dark:border-gray-700/70 hover:bg-gray-100 dark:hover:bg-gray-800 transition" aria-label="Alternar modo oscuro">
            <svg id="iconSun" class="h-5 w-5 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.364-6.364-1.414 1.414M7.05 16.95l-1.414 1.414M16.95 16.95l1.414 1.414M7.05 7.05 5.636 5.636M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <svg id="iconMoon" class="h-5 w-5 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
            </svg>
          </button>

          <a href="{{ route('login') }}" class="font-medium text-gray-700 dark:text-gray-300 hover:text-sky-600 dark:hover:text-sky-400 transition">Iniciar sesión</a>
          <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-sky-600 hover:bg-sky-500 text-white px-4 py-2 rounded-xl shadow-sm transition">
            Registrarse
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 12h14M12 5l7 7-7 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </a>
        </div>

        <!-- Mobile: right side -->
        <div class="md:hidden flex items-center gap-2">
          <!-- Dark -->
          <button id="darkToggleSm" class="p-2 rounded-lg border border-gray-200/70 dark:border-gray-700/70 hover:bg-gray-100 dark:hover:bg-gray-800 transition" aria-label="Alternar modo oscuro">
            <svg id="iconSunSm" class="h-5 w-5 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.364-6.364-1.414 1.414M7.05 16.95l-1.414 1.414M16.95 16.95l1.414 1.414M7.05 7.05 5.636 5.636M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <svg id="iconMoonSm" class="h-5 w-5 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
          </button>

          <!-- Burger -->
          <button id="menuBtn" class="relative h-10 w-10 grid place-items-center rounded-xl border border-gray-200/70 dark:border-gray-700/70 hover:bg-gray-100 dark:hover:bg-gray-800 transition" aria-label="Abrir menú" aria-expanded="false" aria-controls="mobileMenu">
            <div class="hamburger space-y-1.5">
              <span class="block h-0.5 w-6 bg-current transition-all"></span>
              <span class="block h-0.5 w-6 bg-current transition-all"></span>
              <span class="block h-0.5 w-6 bg-current transition-all"></span>
            </div>
          </button>
        </div>
      </div>

      <!-- Mobile Menu -->
      <div id="mobileMenu" class="hidden md:hidden px-4 pb-4 origin-top transition-all">
        <div class="mt-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm overflow-hidden">
          <nav class="flex flex-col divide-y divide-gray-100 dark:divide-gray-800">
            <a href="#about" class="px-4 py-3 font-medium hover:bg-gray-50 dark:hover:bg-gray-800">Sobre</a>
            <a href="#how" class="px-4 py-3 font-medium hover:bg-gray-50 dark:hover:bg-gray-800">Cómo postular</a>
            <a href="#contact" class="px-4 py-3 font-medium hover:bg-gray-50 dark:hover:bg-gray-800">Contacto</a>
          </nav>
          <div class="p-4 flex flex-col gap-2">
            <a href="{{ route('login') }}" class="w-full text-center font-medium border border-gray-200 dark:border-gray-700 rounded-lg px-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-800 transition">Iniciar sesión</a>
            <a href="{{ route('register') }}" class="w-full text-center font-medium bg-sky-600 hover:bg-sky-500 text-white rounded-lg px-4 py-2 transition">Registrarse</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</header>

<!-- ===== Hero ===== -->
<section class="pt-36 pb-16 md:pt-40 bg-gradient-to-b from-white to-gray-50 dark:from-gray-900 dark:to-gray-950">
  <div class="max-w-7xl mx-auto px-6 lg:px-8 grid md:grid-cols-2 gap-12 items-center">
    <div class="space-y-6">
      <div class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-sky-700/80 dark:text-sky-300/90 bg-sky-50 dark:bg-sky-900/30 border border-sky-100/60 dark:border-sky-800/50 px-3 py-1 rounded-full">
        Plataforma para encontrar empleo
      </div>
      <h1 class="text-4xl sm:text-5xl font-extrabold leading-tight">
        Encuentra tu próximo empleo con <span class="text-transparent bg-clip-text bg-gradient-to-r from-sky-600 to-blue-600">WR Consultorías</span>
      </h1>
      <p class="text-lg text-gray-600 dark:text-gray-300">
        Postula rápido desde el móvil y lleva el control de tus candidaturas en un solo lugar.
      </p>
      <div class="flex flex-col sm:flex-row gap-3">
        <a href="{{ route('register') }}" class="group inline-flex items-center justify-center gap-2 bg-sky-600 hover:bg-sky-500 text-white px-6 py-3 rounded-xl shadow-sm transition">
          Crear mi cuenta
          <svg class="h-5 w-5 transition -translate-x-0 group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 12h14M12 5l7 7-7 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </a>
        <a href="#about" class="inline-flex items-center justify-center bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 px-6 py-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 transition">
          Conocer más
        </a>
      </div>

      <!-- Puntos rápidos -->
      <div class="grid grid-cols-2 gap-4 pt-2">
        <div class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
          <span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span> Perfil en minutos
        </div>
        <div class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
          <span class="h-2.5 w-2.5 rounded-full bg-sky-500"></span> Panel de estados
        </div>
        <div class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
          <span class="h-2.5 w-2.5 rounded-full bg-blue-500"></span> Alertas de entrevistas
        </div>
        <div class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
          <span class="h-2.5 w-2.5 rounded-full bg-amber-500"></span> CV seguro
        </div>
      </div>
    </div>

    <div class="relative">
      <div class="absolute -inset-4 rounded-3xl bg-gradient-to-tr from-sky-500/10 to-blue-500/10 blur-2xl"></div>
      <img class="relative w-full rounded-2xl shadow-xl ring-1 ring-black/5 object-cover max-h-[520px]"
           src="https://images.unsplash.com/photo-1519389950473-47ba0277781c?auto=format&fit=crop&w=1400&q=80"
           alt="Aspirante buscando empleo">
    </div>
  </div>
</section>

<!-- ===== About ===== -->
<section id="about" class="py-16 bg-white dark:bg-gray-900">
  <div class="max-w-6xl mx-auto px-6">
    <div class="relative overflow-hidden rounded-3xl border border-gray-200/70 dark:border-gray-800/70 bg-white/70 dark:bg-gray-900/60 backdrop-blur-md">
      <div class="pointer-events-none absolute -top-16 -right-16 h-48 w-48 rounded-full bg-gradient-to-tr from-sky-500/20 to-blue-500/20 blur-2xl"></div>
      <div class="p-6 sm:p-10">
        <header class="text-center mb-6">
          <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Todo lo que necesitas para postular, en un solo lugar</h2>
          <p class="mt-2 text-gray-600 dark:text-gray-300">Aplica, revisa estados y recibe avisos. Sin complicaciones.</p>
        </header>

        <ul class="grid sm:grid-cols-3 gap-4">
          <li class="flex items-start gap-3 rounded-2xl border border-gray-200 dark:border-gray-800 bg-gray-50/70 dark:bg-gray-800/40 p-4">
            <svg class="h-5 w-5 flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 12l4 4L19 6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <div>
              <p class="font-semibold">Postula rápido</p>
              <p class="text-sm text-gray-600 dark:text-gray-400">Desde el móvil, adjunta tu CV y listo.</p>
            </div>
          </li>
          <li class="flex items-start gap-3 rounded-2xl border border-gray-200 dark:border-gray-800 bg-gray-50/70 dark:bg-gray-800/40 p-4">
            <svg class="h-5 w-5 flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 6v6l4 2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <div>
              <p class="font-semibold">Vigila tu avance</p>
              <p class="text-sm text-gray-600 dark:text-gray-400">Panel con estados claros del proceso.</p>
            </div>
          </li>
          <li class="flex items-start gap-3 rounded-2xl border border-gray-200 dark:border-gray-800 bg-gray-50/70 dark:bg-gray-800/40 p-4">
            <svg class="h-5 w-5 flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 17l-5 3 1.5-5.5L4 9l5.5-.5L12 3l2.5 5.5L20 9l-4.5 5.5L17 20z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <div>
              <p class="font-semibold">Recibe avisos</p>
              <p class="text-sm text-gray-600 dark:text-gray-400">Recordatorios y cambios importantes por correo.</p>
            </div>
          </li>
        </ul>

        <div class="mt-6 flex flex-col sm:flex-row items-center justify-center gap-3">
          <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-sky-600 text-white hover:bg-sky-500 transition shadow-sm">
            Iniciar ahora
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 12h14M12 5l7 7-7 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ===== How ===== -->
<section id="how" class="py-20 bg-gray-50 dark:bg-gray-950">
  <div class="max-w-6xl mx-auto px-6">
    <h3 class="text-3xl font-bold text-center mb-12">¿Cómo postular?</h3>
    <div class="grid gap-6 md:grid-cols-3">
      <div class="group rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6 shadow-sm hover:shadow-md transition">
        <span class="text-4xl font-extrabold text-sky-600">1</span>
        <h4 class="text-xl font-semibold mt-3">Crea tu cuenta</h4>
        <p class="text-gray-600 dark:text-gray-300">Completa tu perfil básico.</p>
      </div>
      <div class="group rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6 shadow-sm hover:shadow-md transition">
        <span class="text-4xl font-extrabold text-sky-600">2</span>
        <h4 class="text-xl font-semibold mt-3">Elige vacantes</h4>
        <p class="text-gray-600 dark:text-gray-300">Aplica con tu CV.</p>
      </div>
      <div class="group rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6 shadow-sm hover:shadow-md transition">
        <span class="text-4xl font-extrabold text-sky-600">3</span>
        <h4 class="text-xl font-semibold mt-3">Sigue tu proceso</h4>
        <p class="text-gray-600 dark:text-gray-300">Confirma entrevistas y revisa resultados.</p>
      </div>
    </div>
  </div>
</section>

<!-- ===== Footer ===== -->
<footer id="contact" class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800">
  <div class="max-w-6xl mx-auto px-6 py-10 flex flex-col md:flex-row items-center justify-between gap-4">
    <a href="{{ route('terms') }}" class="text-gray-600 dark:text-gray-400 hover:text-sky-600">Términos y Condiciones</a>
    <p class="text-gray-600 dark:text-gray-400">© {{ date('Y') }} WR Consultorías. Todos los derechos reservados.</p>
  </div>
</footer>

<!-- ===== Scripts ===== -->
<script>
  // Helpers
  const htmlEl = document.documentElement;
  const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

  function setDarkUI(isDark) {
    htmlEl.classList.toggle('dark', isDark);
    localStorage.theme = isDark ? 'dark' : 'light';
    const sun = document.getElementById('iconSun');
    const moon = document.getElementById('iconMoon');
    const sunSm = document.getElementById('iconSunSm');
    const moonSm = document.getElementById('iconMoonSm');
    if (sun && moon) {
      sun.classList.toggle('hidden', !isDark);
      moon.classList.toggle('hidden', isDark);
    }
    if (sunSm && moonSm) {
      sunSm.classList.toggle('hidden', !isDark);
      moonSm.classList.toggle('hidden', isDark);
    }
  }

  // Init dark mode
  setDarkUI(localStorage.theme ? localStorage.theme === 'dark' : prefersDark);

  // Toggle dark (desktop + mobile)
  document.getElementById('darkToggle')?.addEventListener('click', () => {
    setDarkUI(!htmlEl.classList.contains('dark'));
  });
  document.getElementById('darkToggleSm')?.addEventListener('click', () => {
    setDarkUI(!htmlEl.classList.contains('dark'));
  });

  // Navbar scroll effects (shrink + shadow)
  const siteHeader = document.getElementById('siteHeader');
  const shrinkHeader = () => {
    const y = window.scrollY || window.pageYOffset;
    siteHeader.classList.toggle('drop-shadow', y > 4);
    siteHeader.classList.toggle('backdrop-blur', y > 4);
  };
  shrinkHeader();
  window.addEventListener('scroll', shrinkHeader, { passive: true });

  // Mobile menu + burger animation
  const menuBtn = document.getElementById('menuBtn');
  const mobileMenu = document.getElementById('mobileMenu');
  let menuOpen = false;
  menuBtn?.addEventListener('click', () => {
    menuOpen = !menuOpen;
    mobileMenu.classList.toggle('hidden', !menuOpen);
    menuBtn.setAttribute('aria-expanded', String(menuOpen));
    const bars = menuBtn.querySelectorAll('.hamburger span');
    if (bars.length === 3) {
      bars[0].classList.toggle('translate-y-2', menuOpen);
      bars[0].classList.toggle('rotate-45', menuOpen);
      bars[1].classList.toggle('opacity-0', menuOpen);
      bars[2].classList.toggle('-translate-y-2', menuOpen);
      bars[2].classList.toggle('-rotate-45', menuOpen);
    }
  });

  // Cerrar menú al navegar en móvil
  document.querySelectorAll('#mobileMenu a').forEach(a => {
    a.addEventListener('click', () => {
      if (!menuOpen) return;
      menuBtn.click();
    });
  });

  // Subrayado activo según sección visible
  const sections = ['about','how','contact'];
  const links = {};
  sections.forEach(id => { links[id] = Array.from(document.querySelectorAll(`a[href="#${id}"]`)); });
  const obs = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      const id = entry.target.id;
      if (!sections.includes(id)) return;
      links[id]?.forEach(el => {
        el.classList.toggle('text-sky-600', entry.isIntersecting);
        el.classList.toggle('dark:text-sky-400', entry.isIntersecting);
      });
    });
  }, { rootMargin: '-40% 0px -50% 0px', threshold: 0.01 });
  sections.forEach(id => { const el = document.getElementById(id); if (el) obs.observe(el); });
</script>

</body>
</html>
