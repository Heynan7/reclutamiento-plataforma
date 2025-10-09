<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Postulaciones</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
        h1, h2 { text-align: center; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background: #f3f3f3; }
    </style>
</head>
<body>
    <h1>📊 Reporte de Postulaciones</h1>
    <p style="text-align:center;">Fecha de exportación: {{ $fechaExportacion }}</p>

    <h2>Resumen</h2>
    <table>
        <tr><th>Vacantes Activas</th><td>{{ $stats['active_jobs'] }}</td></tr>
        <tr><th>Cerradas</th><td>{{ $stats['closed_jobs'] }}</td></tr>
        <tr><th>Archivadas</th><td>{{ $stats['archived_jobs'] }}</td></tr>
        <tr><th>Total Postulaciones</th><td>{{ $stats['total_apps'] }}</td></tr>
    </table>

    <h2>📆 Últimos 30 días</h2>
    <table>
        <thead><tr><th>Día</th><th>Postulaciones</th></tr></thead>
        <tbody>
        @foreach($appsLast30 as $day => $total)
            <tr><td>{{ $day }}</td><td>{{ $total }}</td></tr>
        @endforeach
        </tbody>
    </table>

    <h2>📅 Por Año</h2>
    <table>
        <thead><tr><th>Año</th><th>Postulaciones</th></tr></thead>
        <tbody>
        @foreach($appsByYear as $year => $total)
            <tr><td>{{ $year }}</td><td>{{ $total }}</td></tr>
        @endforeach
        </tbody>
    </table>

    <h2>📅 Por Mes ({{ $selectedYear }})</h2>
    <table>
        <thead><tr><th>Mes</th><th>Postulaciones</th></tr></thead>
        <tbody>
        @foreach($appsByMonth as $month => $total)
            <tr><td>{{ $month }}</td><td>{{ $total }}</td></tr>
        @endforeach
        </tbody>
    </table>

    <h2>🏆 Top Vacantes</h2>
    <table>
        <thead><tr><th>Vacante</th><th>Postulantes</th></tr></thead>
        <tbody>
        @foreach($topJobs as $job)
            <tr><td>{{ $job->title }}</td><td>{{ $job->applications_count }}</td></tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>
