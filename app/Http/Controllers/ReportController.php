<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Application;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ApplicationsExport;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * 🔹 Centralizamos las consultas del dashboard/export
     */
    private function getReportData(Request $request)
    {
        $year  = $request->input('year', now()->year);
        $month = $request->input('month');
        $from  = $request->input('from');
        $to    = $request->input('to');

        // =============================
        //  Base query para Applications
        // =============================
        $query = Application::query();

        if ($year) {
            $query->whereYear('created_at', $year);
        }
        if ($month) {
            $query->whereMonth('created_at', $month);
        }
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        // KPIs de vacantes
        $stats = [
            'active_jobs'   => Job::where('is_open', true)->whereNull('deleted_at')->count(),
            'closed_jobs'   => Job::where('is_open', false)->whereNull('deleted_at')->count(),
            'archived_jobs' => Job::onlyTrashed()->count(),
            'total_apps'    => $query->count(),
        ];

        // 📆 Últimos 30 días
        $appsLast30 = (clone $query)
            ->selectRaw("DATE(created_at) as day, COUNT(*) as total")
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day');

        // 📅 Por año
        $appsByYear = Application::selectRaw("YEAR(created_at) as year, COUNT(*) as total")
            ->groupBy('year')
            ->orderBy('year')
            ->pluck('total', 'year');

        // 🔄 Estados de vacantes (Activas, Cerradas, Archivadas)
        $jobsByStatus = collect([
            'Activas'    => $stats['active_jobs'],
            'Cerradas'   => $stats['closed_jobs'],
            'Archivadas' => $stats['archived_jobs'],
        ]);

        // 📅 Por mes
        $appsByMonth = (clone $query)
            ->selectRaw("MONTH(created_at) as month, COUNT(*) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->mapWithKeys(function ($value, $month) {
                $meses = [
                    1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr',
                    5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago',
                    9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic',
                ];
                return [$meses[$month] => $value];
            });

        // 🏆 Top vacantes
        $topJobs = Job::withCount('applications')
            ->orderBy('applications_count', 'desc')
            ->take(5)
            ->get();

        return compact('stats', 'appsLast30', 'appsByYear', 'jobsByStatus', 'appsByMonth', 'topJobs');
    }

    /**
     * 🔹 Vista principal del dashboard de reportes
     */
    public function index(Request $request)
    {
        // Lista de años disponibles
        $years = Application::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        $selectedYear = $request->input('year', now()->year);

        // Datos consolidados
        $data = $this->getReportData($request);

        return view('admin.reports.index', array_merge($data, [
            'years' => $years,
            'selectedYear' => $selectedYear
        ]));
    }

    /**
     * 📥 Exportar a Excel
     */
    public function exportExcel(Request $request)
    {
        $filename = 'reporte_postulaciones_' . now()->format('Y-m-d') . '.xlsx';
        return Excel::download(new ApplicationsExport, $filename);
    }

    /**
     * 📥 Exportar a PDF
     */
    public function exportPdf(Request $request)
    {
        $selectedYear = $request->input('year', now()->year);

        // Datos consolidados
        $data = $this->getReportData($request);

        $pdf = Pdf::loadView('admin.reports.pdf', array_merge($data, [
            'fechaExportacion' => now()->format('d/m/Y H:i:s'),
            'selectedYear' => $selectedYear
        ]));

        $filename = 'reporte_postulaciones_' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }
}
