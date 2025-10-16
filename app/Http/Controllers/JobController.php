<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Notifications\ApplicationClosed;

class JobController extends Controller
{
    /**
     * GET /admin/jobs
     * Lista de vacantes con filtros de estado (para el reclutador/admin)
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'active');
        $status = in_array($status, ['active', 'closed', 'archived', 'all'], true) ? $status : 'active';

        $query = Job::query()
            ->withCount('applications')
            ->latest();

        // Filtrar por dueño (reclutador/admin)
        if (Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->role === 'recruiter')) {
            $query->where('created_by', Auth::id());
        }

        // Filtro por estado
        switch ($status) {
            case 'archived':
                $query->onlyTrashed();
                break;
            case 'closed':
                $query->where('is_open', false)->whereNull('deleted_at');
                break;
            case 'active':
                $query->where('is_open', true)->whereNull('deleted_at');
                break;
            case 'all':
                $query->withTrashed();
                break;
        }

        $jobs = $query->paginate(10)->withQueryString();

        return view('admin.jobs.index', compact('jobs', 'status'));
    }

    /**
     * GET /user/jobs
     * Muestra solo vacantes activas y sin postulación previa del usuario.
     */
    public function userIndex()
    {
        $user = Auth::user();

        // Vacantes ya aplicadas por el usuario
        $appliedJobIds = Application::where('user_id', $user->id)
            ->pluck('job_id')
            ->toArray();

        // Mostrar solo vacantes activas y no aplicadas
        $jobs = Job::where('is_open', true)
            ->whereNotIn('id', $appliedJobIds)
            ->whereNull('deleted_at')
            ->latest()
            ->paginate(9);

        return view('user.jobs.index', compact('jobs'));
    }

    /**
     * GET /admin/jobs/create
     */
    public function create()
    {
        return view('admin.jobs.create');
    }

    /**
     * GET /admin/jobs/{job}/edit
     * Muestra el formulario para editar una vacante existente.
     */
    public function edit(Job $job)
    {
        $this->authorizeOwner($job);

        return view('admin.jobs.edit', compact('job'));
    }

    /**
     * POST /admin/jobs
     * Crea una nueva vacante y sube la imagen a Supabase (opcional)
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'       => ['required', 'string', 'max:150'],
            'description' => ['required', 'string'],
            'image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:10240'],
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $upload = $this->uploadToSupabase($request->file('image'));
            if (!$upload['ok']) {
                return back()->withErrors(['image' => '❌ No se pudo subir la imagen a Supabase.'])->withInput();
            }
            $imagePath = $upload['path'];
        }

        Job::create([
            'title'       => $request->title,
            'description' => $request->description,
            'created_by'  => Auth::id(),
            'image'       => $imagePath,
        ]);

        return redirect()->route('admin.jobs.index')->with('success', 'Vacante creada exitosamente.');
    }

    /**
     * PUT/PATCH /admin/jobs/{job}
     * Actualiza los datos y la imagen de una vacante.
     */
    public function update(Request $request, Job $job)
    {
        $this->authorizeOwner($job);

        $request->validate([
            'title'       => ['required', 'string', 'max:150'],
            'description' => ['required', 'string'],
            'image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:10240'],
        ]);

        $data = $request->only('title', 'description');

        if ($request->hasFile('image')) {
            if ($job->image) {
                $this->deleteFromSupabase($job->image);
            }

            $upload = $this->uploadToSupabase($request->file('image'));
            if (!$upload['ok']) {
                return back()->withErrors(['image' => '❌ No se pudo subir la nueva imagen a Supabase.'])->withInput();
            }
            $data['image'] = $upload['path'];
        }

        $job->update($data);

        return redirect()->route('admin.jobs.index')->with('success', 'Vacante actualizada correctamente.');
    }

    /**
     * PATCH /admin/jobs/{job}/toggle
     * Alterna entre abierto y cerrado, notificando al cerrar.
     */
    public function toggleStatus(Job $job)
    {
        $this->authorizeOwner($job);

        $job->is_open = !$job->is_open;
        $job->save();

        // Si se cierra, notificar candidatos activos
        if (!$job->is_open) {
            $applications = Application::with('user')
                ->where('job_id', $job->id)
                ->whereIn('status', ['submitted', 'shortlisted', 'interview_scheduled'])
                ->get();

            foreach ($applications as $application) {
                $application->update([
                    'status'            => 'closed',
                    'status_updated_at' => now(),
                ]);

                try {
                    if ($application->user) {
                        $application->user->notify(new ApplicationClosed($application));
                    }
                } catch (\Throwable $e) {
                    report($e);
                }
            }
        }

        return back()->with('success', $job->is_open
            ? 'Vacante reabierta correctamente.'
            : 'Vacante cerrada y candidatos notificados.');
    }

    /**
     * DELETE /admin/jobs/{job}
     * Archiva o elimina una vacante (según tenga postulantes)
     */
    public function destroy(Job $job)
    {
        $this->authorizeOwner($job);

        $appsCount = $job->applications()->count();

        if ($appsCount > 0) {
            // Cierra y notifica candidatos antes de archivar
            $applications = Application::with('user')
                ->where('job_id', $job->id)
                ->whereIn('status', ['submitted', 'shortlisted', 'interview_scheduled'])
                ->get();

            foreach ($applications as $application) {
                $application->update([
                    'status'            => 'closed',
                    'status_updated_at' => now(),
                ]);

                try {
                    if ($application->user) {
                        $application->user->notify(new ApplicationClosed($application));
                    }
                } catch (\Throwable $e) {
                    report($e);
                }
            }

            $job->delete(); // soft delete
            return redirect()
                ->route('admin.jobs.index', ['status' => 'archived'])
                ->with('success', 'Vacante archivada. Los candidatos fueron notificados.');
        }

        // Eliminar definitivamente si no tiene postulaciones
        if ($job->image) {
            $this->deleteFromSupabase($job->image);
        }

        $job->forceDelete();

        return redirect()
            ->route('admin.jobs.index')
            ->with('success', 'Vacante eliminada definitivamente.');
    }

    /**
     * POST /admin/jobs/{id}/restore
     * Restaura una vacante archivada.
     */
    public function restore($id)
    {
        $job = Job::onlyTrashed()->findOrFail($id);

        $this->authorizeOwner($job);

        $job->restore();

        return back()->with('success', 'Vacante restaurada correctamente.');
    }

    /**
     * GET /admin/jobs/{job}/image
     * Devuelve la imagen almacenada en Supabase.
     */
public function viewImage(\App\Models\Job $job)
{
    if (!$job->image) {
        abort(404, 'La vacante no tiene imagen.');
    }

    // ✅ Tu bucket "jobs" tiene una carpeta interna "jobs/"
    $path = ltrim($job->image, '/');

    $publicUrl = rtrim(env('SUPABASE_URL'), '/') .
        '/storage/v1/object/public/' .
        env('SUPABASE_BUCKET_JOBS', 'jobs') . '/' . $path;

    return redirect()->away($publicUrl);
}


    /* =========================================================
     | Helpers privados
     ========================================================= */

    private function authorizeOwner(Job $job): void
    {
        if (!Auth::check() || $job->created_by !== Auth::id()) {
            abort(403);
        }
    }

        private function supabaseHeaders(): array
    {
        $token = env('SUPABASE_SERVICE_ROLE');
        return [
            'apikey'        => $token,
            'Authorization' => 'Bearer ' . $token,
        ];
    }

    private function supabaseObjectUrl(string $path): string
    {
        $base   = rtrim(env('SUPABASE_URL'), '/');
        $bucket = env('SUPABASE_BUCKET_JOBS');
        $clean  = ltrim($path, '/');
        return "{$base}/storage/v1/object/{$bucket}/{$clean}";
    }

    private function uploadToSupabase(\Illuminate\Http\UploadedFile $file): array
    {
        try {
            $filename  = (string) Str::uuid() . '.' . $file->getClientOriginalExtension();
            $imagePath = 'jobs/' . $filename;

            $resp = Http::withHeaders($this->supabaseHeaders())
                ->attach('file', file_get_contents($file->getRealPath()), $filename)
                ->post($this->supabaseObjectUrl($imagePath));

            if ($resp->failed()) {
                return ['ok' => false, 'path' => null];
            }

            return ['ok' => true, 'path' => $imagePath];
        } catch (\Throwable $e) {
            report($e);
            return ['ok' => false, 'path' => null];
        }
    }

    private function deleteFromSupabase(string $path): void
    {
        try {
            Http::withHeaders($this->supabaseHeaders())
                ->delete($this->supabaseObjectUrl($path));
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
