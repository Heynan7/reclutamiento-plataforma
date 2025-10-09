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
        abort(404, 'La vacante no tiene imagen.');
    }

    // 🔹 Como tu bucket tiene la carpeta jobs/, no hacemos str_replace
    $path = ltrim($job->image, '/');

    // 🔹 Construimos la URL pública completa (nota el /public/jobs/jobs/)
    $publicUrl = rtrim(env('SUPABASE_URL'), '/') .
        '/storage/v1/object/public/' .
        env('SUPABASE_BUCKET_JOBS', 'jobs') . '/' . $path;

    return redirect()->away($publicUrl);
}



}
