<x-app-layout>
    <x-slot name="header">
        <div class="text-center">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">📊 Reportes y Estadísticas</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Visualiza métricas clave, evolución de postulaciones y ranking de vacantes
            </p>
        </div>
    </x-slot>

    <main class="max-w-6xl mx-auto px-4 py-10 space-y-8">

        {{-- 🔹 Filtros --}}
        <form method="GET" class="flex flex-wrap gap-4 items-end bg-white dark:bg-gray-900 p-4 rounded-lg shadow border">
            <div>
                <label class="block text-sm text-gray-600 dark:text-gray-300">Año</label>
                <select name="year" class="border rounded px-3 pr-8 py-1">
                    @foreach($years as $year)
                        <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm text-gray-600 dark:text-gray-300">Mes</label>
                <select name="month" class="border rounded px-3 pr-8 py-1">
                    <option value="">--Todos--</option>
                    @foreach(['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'] as $i => $mes)
                        <option value="{{ $i+1 }}" {{ request('month') == $i+1 ? 'selected' : '' }}>
                            {{ $mes }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm text-gray-600 dark:text-gray-300">Desde</label>
                <input type="date" name="from" value="{{ request('from') }}" class="border rounded px-2 py-1">
            </div>

            <div>
                <label class="block text-sm text-gray-600 dark:text-gray-300">Hasta</label>
                <input type="date" name="to" value="{{ request('to') }}" class="border rounded px-2 py-1">
            </div>

            <div>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    Filtrar
                </button>
            </div>
        </form>

        {{-- 🔹 Botones exportar --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.reports.export.excel', request()->query()) }}"
               class="inline-flex items-center gap-2 bg-blue-600 text-white px-5 py-2 rounded-md hover:bg-blue-700">
               📥 Exportar a Excel
            </a>
            <a href="{{ route('admin.reports.export.pdf', request()->query()) }}"
               class="inline-flex items-center gap-2 bg-red-600 text-white px-5 py-2 rounded-md hover:bg-red-700">
               📄 Exportar a PDF
            </a>
        </div>

        {{-- 🔹 KPIs --}}
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-gray-900 p-5 rounded-lg shadow text-center border">
                <p class="text-sm text-gray-500">Vacantes Activas</p>
                <p class="text-2xl font-bold text-green-600">{{ $stats['active_jobs'] }}</p>
            </div>
            <div class="bg-white dark:bg-gray-900 p-5 rounded-lg shadow text-center border">
                <p class="text-sm text-gray-500">Cerradas</p>
                <p class="text-2xl font-bold text-red-600">{{ $stats['closed_jobs'] }}</p>
            </div>
            <div class="bg-white dark:bg-gray-900 p-5 rounded-lg shadow text-center border">
                <p class="text-sm text-gray-500">Archivadas</p>
                <p class="text-2xl font-bold text-yellow-500">{{ $stats['archived_jobs'] }}</p>
            </div>
            <div class="bg-white dark:bg-gray-900 p-5 rounded-lg shadow text-center border">
                <p class="text-sm text-gray-500">Total Postulaciones</p>
                <p class="text-2xl font-bold text-blue-600">{{ $stats['total_apps'] }}</p>
            </div>
        </section>

        {{-- 🔹 Gráficas --}}
        <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow border">
                <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-3">📆 Últimos 30 días</h3>
                <div class="h-[220px]"><canvas id="appsLast30"></canvas></div>
            </div>

            <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow border">
                <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-3">📅 Por Año</h3>
                <div class="h-[220px]"><canvas id="appsByYear"></canvas></div>
            </div>

            {{-- 🔄 Estados --}}
            <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow border">
                <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-3">🔄 Estados</h3>
                <div class="h-[220px]"><canvas id="appsByStatus"></canvas></div>
            </div>

            <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow border">
                <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-3">🏆 Top Vacantes</h3>
                <div class="h-[220px]"><canvas id="topJobsChart"></canvas></div>
            </div>

            <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow border lg:col-span-2">
                <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-3">📅 Por Mes</h3>
                <div class="h-[220px]"><canvas id="appsByMonth"></canvas></div>
            </div>
        </section>
    </main>

    {{-- 📌 Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    // 🔄 Estados
    new Chart(document.getElementById('appsByStatus'), {
        type: 'pie',
        data: {
            labels: {!! json_encode($jobsByStatus->keys()) !!},
            datasets: [{
                data: {!! json_encode($jobsByStatus->values()) !!},
                backgroundColor: [
                    '#10b981', // Verde (Activas)
                    '#ef4444', // Rojo (Cerradas)
                    '#f59e0b'  // Amarillo (Archivadas)
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'rect'
                    }
                }
            }
        }
    });

    // 📆 Últimos 30 días
    new Chart(document.getElementById('appsLast30'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($appsLast30->keys()) !!},
            datasets: [{
                label: 'Postulaciones',
                data: {!! json_encode($appsLast30->values()) !!},
                backgroundColor: '#3b82f6',
                maxBarThickness: 40
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    // 📅 Por año
    new Chart(document.getElementById('appsByYear'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($appsByYear->keys()) !!},
            datasets: [{
                label: 'Postulaciones',
                data: {!! json_encode($appsByYear->values()) !!},
                backgroundColor: '#10b981',
                maxBarThickness: 40
            }]
        },
        options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false }
    });

    // 🏆 Top Vacantes
    new Chart(document.getElementById('topJobsChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($topJobs->pluck('title')) !!},
            datasets: [{
                label: 'Postulantes',
                data: {!! json_encode($topJobs->pluck('applications_count')) !!},
                backgroundColor: '#f97316',
                maxBarThickness: 40
            }]
        },
        options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false }
    });

    // 📅 Por mes
    new Chart(document.getElementById('appsByMonth'), {
        type: 'line',
        data: {
            labels: {!! json_encode($appsByMonth->keys()) !!},
            datasets: [{
                label: 'Postulaciones',
                data: {!! json_encode($appsByMonth->values()) !!},
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99,102,241,0.3)',
                fill: true,
                tension: 0.3
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });
    </script>
</x-app-layout>
