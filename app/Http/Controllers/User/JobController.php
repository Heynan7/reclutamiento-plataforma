<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class JobController extends Controller
{
    /**
     * 🧠 Listar vacantes activas (todas las abiertas, aunque el usuario ya haya aplicado)
     */
    public function index()
    {
        $user = Auth::user();

        // Solo mostrar vacantes activas y no archivadas
        $jobs = Job::where('is_open', true)
            ->whereNull('deleted_at')
            ->latest()
            ->paginate(9);

        return view('user.jobs.index', compact('jobs'));
    }

    /**
     * 📄 Mostrar detalle de una vacante específica
     */
    public function show(Job $job)
    {
        $user = Auth::user();

        // Verifica si ya aplicó
        $alreadyApplied = Application::where('user_id', $user->id)
            ->where('job_id', $job->id)
            ->exists();

        // Si la vacante está cerrada o archivada, redirige
        if (!$job->is_open || !is_null($job->deleted_at)) {
            return redirect()
                ->route('user.jobs.index')
                ->with('error', 'Esta vacante ya no está disponible.');
        }

        // Pasa el flag a la vista para mostrar botón deshabilitado si ya aplicó
        return view('user.jobs.show', compact('job', 'alreadyApplied'));
    }

    /**
     * 🖼️ Mostrar imagen pública de la vacante (desde Supabase)
     */
    public function viewImage(Job $job)
    {
        if (!$job->image) {
            abort(404);
        }

        $path = ltrim($job->image, '/');

        $response = Http::withHeaders([
            'apikey'        => env('SUPABASE_SERVICE_ROLE'),
            'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_ROLE'),
        ])->get(env('SUPABASE_URL') . '/storage/v1/object/' . env('SUPABASE_BUCKET_JOBS') . '/' . $path);

        if ($response->failed()) {
            abort(500, '❌ No se pudo obtener la imagen desde Supabase.');
        }

        return response($response->body(), 200)
            ->header('Content-Type', $response->header('Content-Type') ?? 'image/jpeg')
            ->header('Content-Disposition', 'inline; filename="' . basename($job->image) . '"');
    }

}
