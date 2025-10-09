<?php

namespace App\Exports;

use App\Models\Job;
use App\Models\Application;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class ApplicationsExport implements FromArray, WithTitle
{
    public function array(): array
    {
        // KPIs
        $stats = [
            'Vacantes Activas'   => Job::where('is_open', true)->whereNull('deleted_at')->count(),
            'Cerradas'           => Job::where('is_open', false)->whereNull('deleted_at')->count(),
            'Archivadas'         => Job::onlyTrashed()->count(),
            'Total Postulaciones'=> Application::count(),
        ];

        // Últimos 30 días
        $appsLast30 = Application::selectRaw("DATE(created_at) as day, COUNT(*) as total")
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day')
            ->toArray();

        // Por año
        $appsByYear = Application::selectRaw("YEAR(created_at) as year, COUNT(*) as total")
            ->groupBy('year')
            ->orderBy('year')
            ->pluck('total', 'year')
            ->toArray();

        // Por mes (año actual)
        $appsByMonth = Application::selectRaw("MONTH(created_at) as month, COUNT(*) as total")
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Top 5 vacantes
        $topJobs = Job::withCount('applications')
            ->orderBy('applications_count', 'desc')
            ->take(5)
            ->get()
            ->map(fn($job) => [$job->title, $job->applications_count])
            ->toArray();

        // Armar array final para Excel
        return [
            ['📊 Resumen'],
            ['Vacantes Activas', $stats['Vacantes Activas']],
            ['Cerradas', $stats['Cerradas']],
            ['Archivadas', $stats['Archivadas']],
            ['Total Postulaciones', $stats['Total Postulaciones']],
            [],
            ['📆 Últimos 30 días'],
            ...collect($appsLast30)->map(fn($v,$k)=>[$k,$v])->values()->toArray(),
            [],
            ['📅 Por Año'],
            ...collect($appsByYear)->map(fn($v,$k)=>[$k,$v])->values()->toArray(),
            [],
            ['📅 Por Mes ('.now()->year.')'],
            ...collect($appsByMonth)->map(fn($v,$k)=>[$k,$v])->values()->toArray(),
            [],
            ['🏆 Top 5 Vacantes'],
            ['Vacante','Postulantes'],
            ...$topJobs,
            [],
            ['📅 Fecha de Exportación', now()->format('d/m/Y H:i:s')],
        ];
    }

    public function title(): string
    {
        return 'Reporte';
    }
}
