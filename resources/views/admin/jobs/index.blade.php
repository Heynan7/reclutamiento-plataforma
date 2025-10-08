<x-app-layout> 
    <x-slot name="header">
        @php
            $routeHasDashboard = \Illuminate\Support\Facades\Route::has('admin.dashboard');
            $routeHasIndex     = \Illuminate\Support\Facades\Route::has('admin.index');
            $backUrl = $routeHasDashboard
                ? route('admin.dashboard')
                : ($routeHasIndex ? route('admin.index') : url('/admin'));
        @endphp

        <div class="flex items-center justify-between">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-gray-100">
                Gestión de Vacantes
            </h2>

            <div class="flex items-center gap-2">
                <!-- Botón: Volver al panel -->
                <a href="{{ $backUrl }}"
                   class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-medium
                          bg-gradient-to-r from-gray-800 to-gray-700 text-white shadow hover:opacity-95 active:opacity-90 transition">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M15 18l-6-6 6-6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Volver al panel
                </a>

                <!-- Botón: Nueva Vacante (tu original) -->
                <a href="{{ route('admin.jobs.create') }}"
                   class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-medium
                          bg-gradient-to-r from-sky-600 to-indigo-600 text-white shadow hover:opacity-95 active:opacity-90 transition">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 5v14M5 12h14"/></svg>
                    Nueva Vacante
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8">
        {{-- Mensajes flash --}}
        @foreach (['success','status'] as $flash)
          @if (session($flash))
            <div class="mt-4 rounded-xl border border-green-200/70 bg-green-50 text-green-800 px-4 py-3 shadow-sm">
              {{ session($flash) }}
            </div>
          @endif
        @endforeach

        {{-- Tabs de filtro --}}
        <div class="mt-6 flex flex-wrap gap-2">
            <a href="{{ route('admin.jobs.index', ['status' => 'active']) }}"
               class="px-4 py-2 rounded-full text-sm font-medium transition
               {{ $status === 'active' ? 'bg-sky-600 text-white shadow' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
               Activas
            </a>
            <a href="{{ route('admin.jobs.index', ['status' => 'closed']) }}"
               class="px-4 py-2 rounded-full text-sm font-medium transition
               {{ $status === 'closed' ? 'bg-rose-600 text-white shadow' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
               Cerradas
            </a>
            <a href="{{ route('admin.jobs.index', ['status' => 'archived']) }}"
               class="px-4 py-2 rounded-full text-sm font-medium transition
               {{ $status === 'archived' ? 'bg-amber-500 text-white shadow' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
               Archivadas
            </a>
            <a href="{{ route('admin.jobs.index', ['status' => 'all']) }}"
               class="px-4 py-2 rounded-full text-sm font-medium transition
               {{ $status === 'all' ? 'bg-gray-900 text-white shadow' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
               Todas
            </a>
        </div>

        {{-- Tabla en tarjeta --}}
        <div class="mt-6 overflow-hidden rounded-2xl border border-gray-200 dark:border-gray-800 bg-white/90 dark:bg-gray-900/90 shadow-sm">
          <div class="overflow-x-auto">
            <table class="w-full text-sm">
              <thead class="bg-gray-50/80 dark:bg-gray-800/60 sticky top-0 z-10">
                <tr class="text-left text-gray-700 dark:text-gray-200">
                  <th class="px-4 py-3 font-semibold">Imagen</th>
                  <th class="px-4 py-3 font-semibold">Título</th>
                  <th class="px-4 py-3 font-semibold">Descripción</th>
                  <th class="px-4 py-3 font-semibold">Fecha Publicación</th>
                  <th class="px-4 py-3 font-semibold">Estado</th>
                  <th class="px-4 py-3 font-semibold">Postulantes</th>
                  <th class="px-4 py-3 font-semibold">Acciones</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($jobs as $job)
                  @php $hasApps = $job->applications_count > 0; @endphp

                  {{-- Formularios --}}
                  <form id="form-toggle-{{ $job->id }}" action="{{ route('admin.jobs.toggle', $job) }}" method="POST">@csrf @method('PATCH')</form>
                  <form id="form-destroy-{{ $job->id }}" action="{{ route('admin.jobs.destroy', $job) }}" method="POST">@csrf @method('DELETE')</form>
                  <form id="form-restore-{{ $job->id }}" action="{{ route('admin.jobs.restore', $job->id) }}" method="POST">@csrf @method('PATCH')</form>

                  <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition">
                    {{-- Imagen --}}
                    <td class="px-4 py-3">
                      <div class="w-12 h-12 rounded-xl overflow-hidden ring-1 ring-gray-200/70 dark:ring-gray-700 mx-auto">
                        @if($job->image)
                          <a href="{{ route('admin.jobs.image', $job->id) }}" target="_blank">
                            <img src="{{ route('admin.jobs.image', $job->id) }}"
                                 alt="Imagen de {{ $job->title }}"
                                 class="w-full h-full object-cover hover:scale-110 transition-transform duration-300">
                          </a>
                        @else
                          <div class="w-full h-full grid place-items-center text-gray-400">—</div>
                        @endif
                      </div>
                    </td>

                    {{-- Título (colapsable) --}}
                    <td class="px-4 py-3 align-top">
                      <div class="space-y-1">
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100
                                  line-clamp-2" data-collapsible data-lines="2">
                          {{ $job->title }}
                        </p>
                        <button type="button" class="hidden text-xs font-medium text-sky-700 hover:underline"
                                data-toggle>
                          Ver más
                        </button>
                      </div>
                    </td>

                    {{-- Descripción (colapsable) --}}
                    <td class="px-4 py-3 align-top">
                      @php
                        $descVisible = Str::limit(strip_tags($job->description), 180);
                        $descFull = strip_tags($job->description);
                      @endphp
                      <div class="space-y-1">
                        <p class="text-sm text-gray-700 dark:text-gray-300 line-clamp-3"
                           data-collapsible data-lines="3"
                           data-full="{{ e($descFull) }}">
                          {{ $descVisible }}
                        </p>
                        <button type="button" class="hidden text-xs font-medium text-sky-700 hover:underline"
                                data-toggle>
                          Ver más
                        </button>
                      </div>
                    </td>

                    {{-- Fecha --}}
                    <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                      {{ $job->created_at->format('d/m/Y') }}
                    </td>

                    {{-- Estado --}}
                    <td class="px-4 py-3">
                      @if (!$job->trashed())
                        <button
                          type="submit"
                          form="form-toggle-{{ $job->id }}"
                          class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-semibold shadow-sm transition
                                 {{ $job->is_open ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'bg-rose-600 text-white hover:bg-rose-700' }}"
                          onclick="return handleToggle(event, {{ $job->is_open ? 'true' : 'false' }}, {{ $job->id }})">
                          <span class="inline-block h-2 w-2 rounded-full {{ $job->is_open ? 'bg-white' : 'bg-white' }}"></span>
                          {{ $job->is_open ? 'Abierta' : 'Cerrada' }}
                        </button>
                      @else
                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 text-amber-800 px-3 py-1 text-xs font-semibold">
                          Archivada
                        </span>
                      @endif
                    </td>

                    {{-- Postulantes --}}
                    <td class="px-4 py-3 text-gray-900 dark:text-gray-100 font-semibold">
                      {{ $job->applications_count }}
                    </td>

                    {{-- Acciones --}}
                    <td class="px-4 py-3">
                      <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('admin.jobs.edit', $job) }}"
                           class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-medium
                                  bg-sky-50 text-sky-700 hover:bg-sky-100 ring-1 ring-sky-200">
                          Editar
                        </a>

                        <a href="{{ route('admin.jobs.applications', $job->id) }}"
                           class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-medium
                                  bg-indigo-50 text-indigo-700 hover:bg-indigo-100 ring-1 ring-indigo-200">
                          Ver postulantes
                        </a>

                        @if ($job->trashed())
                          <button type="submit"
                                  form="form-restore-{{ $job->id }}"
                                  class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-medium
                                         bg-emerald-50 text-emerald-700 hover:bg-emerald-100 ring-1 ring-emerald-200"
                                  onclick="return handleRestore(event, {{ $job->id }})">
                            Restaurar
                          </button>
                        @else
                          <button type="submit"
                                  form="form-destroy-{{ $job->id }}"
                                  class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-medium
                                         {{ !$hasApps ? 'bg-rose-50 text-rose-700 hover:bg-rose-100 ring-1 ring-rose-200' : 'bg-zinc-50 text-zinc-700 hover:bg-zinc-100 ring-1 ring-zinc-200' }}"
                                  onclick="return handleDelete(event, {{ $hasApps ? 'true' : 'false' }}, {{ $job->is_open ? 'true' : 'false' }}, {{ $job->id }})">
                            Eliminar / Archivar
                          </button>
                        @endif
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="7" class="px-4 py-6 text-center text-gray-500">No hay vacantes en esta categoría.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <div class="px-4 sm:px-6 lg:px-8 py-4">
            {{ $jobs->links() }}
          </div>
        </div>
    </div>

    {{-- CDN de SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Colapsables (solo UI) --}}
    <script>
      // Detecta si el texto está truncado y muestra el botón "Ver más"
      function initCollapsibles() {
        document.querySelectorAll('[data-collapsible]').forEach((p) => {
          const btn = p.parentElement.querySelector('[data-toggle]');
          if (!btn) return;

          const full = p.dataset.full;
          const isClamped = p.scrollHeight > p.clientHeight + 2;
          if (isClamped || full) btn.classList.remove('hidden');

          let expanded = false;
          btn.addEventListener('click', () => {
            expanded = !expanded;
            if (expanded) {
              if (full) p.textContent = full;
              p.classList.remove('line-clamp-2','line-clamp-3');
              btn.textContent = 'Ver menos';
            } else {
              const lines = p.dataset.lines || 3;
              p.classList.remove('line-clamp-2','line-clamp-3');
              p.classList.add(lines == 2 ? 'line-clamp-2' : 'line-clamp-3');
              if (full) {
                const previewLen = 180;
                p.textContent = full.length > previewLen ? full.slice(0, previewLen) + '…' : full;
              }
              btn.textContent = 'Ver más';
            }
          });
        });
      }
      document.addEventListener('DOMContentLoaded', initCollapsibles);

      // Abrir / Cerrar
      function handleToggle(e, isOpen, jobId) {
        e.preventDefault();
        Swal.fire({
          title: isOpen ? "¿Cerrar vacante?" : "¿Reabrir vacante?",
          text: isOpen ? "Los candidatos ya no podrán postularse." : "Nuevos candidatos podrán postularse.",
          icon: isOpen ? "warning" : "info",
          showCancelButton: true,
          confirmButtonText: isOpen ? "Sí, cerrar" : "Sí, reabrir",
          cancelButtonText: "Cancelar"
        }).then((result) => {
          if (result.isConfirmed) {
            document.getElementById("form-toggle-" + jobId).submit();
          }
        });
      }

      // Eliminar / Archivar
      function handleDelete(e, hasApps, isOpen, jobId) {
        e.preventDefault();
        if (!hasApps) {
          Swal.fire({
            title: "¿Eliminar vacante?",
            text: "Esto eliminará definitivamente la vacante.",
            icon: "error",
            showCancelButton: true,
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "Cancelar"
          }).then((r) => { if (r.isConfirmed) document.getElementById("form-destroy-" + jobId).submit(); });
        } else if (hasApps && isOpen) {
          Swal.fire({
            icon: "warning",
            title: "Acción no permitida",
            text: "Esta vacante tiene postulantes. Primero ciérrala antes de eliminar."
          });
        } else {
          Swal.fire({
            title: "¿Archivar vacante?",
            text: "La vacante tiene postulantes. Se archivará y se conservará el historial.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Sí, archivar",
            cancelButtonText: "Cancelar"
          }).then((r) => { if (r.isConfirmed) document.getElementById("form-destroy-" + jobId).submit(); });
        }
      }

      // Restaurar
      function handleRestore(e, jobId) {
        e.preventDefault();
        Swal.fire({
          title: "¿Restaurar vacante?",
          text: "La vacante volverá a estar disponible.",
          icon: "info",
          showCancelButton: true,
          confirmButtonText: "Sí, restaurar",
          cancelButtonText: "Cancelar"
        }).then((r) => { if (r.isConfirmed) document.getElementById("form-restore-" + jobId).submit(); });
      }
    </script>

    {{-- Hint de scroll en móvil --}}
    <style>
      .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
      .line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
      @media (max-width: 640px) {
        table { min-width: 760px; }
      }
    </style>
</x-app-layout>
