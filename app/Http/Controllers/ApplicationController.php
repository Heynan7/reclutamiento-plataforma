<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\Job;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

// Jobs (IA / análisis)
use App\Jobs\AnalyzeCvJob;

// Notificaciones
use App\Notifications\ApplicationSubmitted;
use App\Notifications\ApplicationShortlisted;
use App\Notifications\InterviewPreliminaryScheduled;
use App\Notifications\InterviewDeepScheduled;
use App\Notifications\ApplicationClosed;
use App\Notifications\PsychotestAssigned;
use App\Notifications\SocioeconomicStudyAssigned;
use App\Notifications\ApplicationHired;
use App\Notifications\ApplicationRejected;

class ApplicationController extends Controller
{
    /**
     * 📋 Listado de postulaciones del usuario (aspirante)
     */
    public function index()
    {
        $applications = Application::with(['job' => fn ($q) => $q->withTrashed(), 'ranking'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('user.applications.index', compact('applications'));
    }

    /**
     * 📝 Guardar postulación y subir CV a Supabase
     */
    public function store(Request $request)
    {
        $request->validate([
            'job_id'       => ['required', 'integer', 'exists:jobs,id'],
            'cv_file'      => ['required', 'file', 'mimes:pdf', 'max:5120'],
            'cover_letter' => ['nullable', 'string'],
        ]);

        $user  = Auth::user();
        $jobId = (int) $request->job_id;

        // Evitar duplicado
        if (Application::where('user_id', $user->id)->where('job_id', $jobId)->exists()) {
            return back()->with('error', 'Ya aplicaste a esta vacante.');
        }

        // Validar que la vacante esté abierta
        $job = Job::query()->where('id', $jobId)->where('is_open', true)->first();
        if (!$job) {
            return back()->with('error', 'Esta vacante no está disponible.');
        }

        // Subida del CV a Supabase
        $file = $request->file('cv_file');
        $filename = 'cvs/' . Str::uuid() . '.pdf';

        $resp = Http::withHeaders($this->supabaseHeaders())
            ->attach('file', file_get_contents($file->getRealPath()), basename($filename))
            ->post($this->supabaseObjectUrl(env('SUPABASE_BUCKET', 'public'), $filename));

        if ($resp->failed()) {
            return back()->with('error', 'Error al subir el CV a Supabase.');
        }

        $data = [
            'user_id'           => $user->id,
            'job_id'            => $jobId,
            'cv_file'           => $filename,
            'cover_letter'      => $request->cover_letter,
            'status'            => 'submitted',
            'status_updated_at' => now(),
        ];

        if (Schema::hasColumn('applications', 'job_title_snapshot')) {
            $data['job_title_snapshot'] = $job->title;
        }

        $application = Application::create($data);

        // Notificación al usuario
        try {
            $user->notify(new ApplicationSubmitted($application));
        } catch (\Throwable $e) {
            report($e);
        }

        // Análisis IA de CV
        dispatch(new AnalyzeCvJob($application->id));

        return redirect()
            ->route('user.jobs.index')
            ->with('success', 'Postulación enviada con éxito. El sistema está analizando tu CV.');
    }

    /**
     * 📂 Descargar CV desde Supabase
     */
    public function downloadCv(Application $application)
    {
        $user = Auth::user();

        if ($user->role !== 'admin' && $application->user_id !== $user->id) {
            abort(403);
        }

        if (!$application->cv_file) {
            abort(404, 'Este candidato no subió CV.');
        }

        $path = ltrim($application->cv_file, '/');

        $response = Http::withHeaders($this->supabaseHeaders())
            ->get($this->supabaseObjectUrl(env('SUPABASE_BUCKET', 'public'), $path));

        if ($response->failed()) {
            abort(500, 'No se pudo obtener el archivo desde Supabase.');
        }

        return new StreamedResponse(fn () => print($response->body()), 200, [
            'Content-Type'        => $response->header('Content-Type', 'application/pdf'),
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
        ]);
    }

    /**
     * 👥 Mostrar postulantes de una vacante (admin)
     */
    public function byJob($jobId)
    {
        $job = Job::with(['applications.user', 'applications.ranking'])->findOrFail($jobId);

        if ($job->created_by && (int) $job->created_by !== (int) Auth::id()) {
            abort(403);
        }

        $applications = $job->applications->sortByDesc(fn ($a) => $a->ranking->score ?? 0);

        return view('admin.jobs.applications', compact('job', 'applications'));
    }

    /**
     * 👁️ Detalle de una postulación (admin)
     */
    public function showAdmin(Application $application)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $application->load(['user', 'job' => fn ($q) => $q->withTrashed(), 'ranking']);

        return view('admin.applications.show', compact('application'));
    }

    /**
     * 🧠 Ver resultados del psicotest (admin)
     */
    public function showPsychotestResults(Application $application)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $application->load(['user', 'job']);

        if (!$application->psychotest_answers) {
            return back()->with('info', 'El candidato aún no ha completado la prueba psicométrica.');
        }

        return view('admin.applications.psychotest-results', compact('application'));
    }

/**
 * 📌 Actualizar estado individual
 */
public function updateStatus(Request $request, Application $application)
{
    $request->validate([
        'status'                    => ['required', 'string'],
        'interview_at'              => ['nullable', 'date', 'after:now'],
        'interview_channel'         => ['nullable', 'string', 'max:100'],
        'interview_link'            => ['nullable', 'string', 'max:255'],
        'socioeconomic_link'        => ['nullable', 'string', 'max:255'],
        'psychotest_link'           => ['nullable', 'string', 'max:255'],
        'use_internal_psychotest'   => ['nullable'],
        'interview_message'         => ['nullable', 'string', 'max:500'],
        'message'                   => ['nullable', 'string', 'max:500'],
    ]);

    $newStatus = $request->status;

    $updateData = [
        'status'            => $newStatus,
        'status_updated_at' => now(),
    ];

    $needsAvailability = in_array($newStatus, [
        'interview_scheduled',
        'interview_deep',
        'socioeconomic_study',
    ], true);

    // 📅 Entrevistas
    if (str_starts_with($newStatus, 'interview')) {
        $updateData = array_merge($updateData, [
            'interview_at'      => $request->interview_at,
            'interview_channel' => $request->interview_channel,
            'interview_link'    => $request->interview_link,
            'interview_message' => $request->interview_message,
        ]);
    }

    // 🧠 Psicométrico
    if ($newStatus === 'psychotest') {
        // Si el reclutador reasigna la prueba, se limpia todo
        if ($request->boolean('use_internal_psychotest')) {
            $updateData['psychotest_link'] = null;
        } elseif ($request->filled('psychotest_link')) {
            $updateData['psychotest_link'] = $request->psychotest_link;
        }

        // 💡 Al reasignar el test, se limpian los datos previos
        $updateData['psychotest_score']        = null;
        $updateData['psychotest_answers']      = null;
        $updateData['psychotest_completed_at'] = null;

    } elseif ($application->status === 'psychotest' && $application->psychotest_completed_at) {
        // ✅ Si el candidato ya completó la prueba y solo cambiamos de fase,
        // conservamos los resultados (NO los borramos)
        // No hacemos nada aquí
    } else {
        // 🧹 En cualquier otro caso (por ejemplo, estaba en psychotest pero no la hizo)
        $updateData['psychotest_link']         = null;
        $updateData['psychotest_score']        = null;
        $updateData['psychotest_answers']      = null;
        $updateData['psychotest_completed_at'] = null;
    }

    // 🏠 Estudio socioeconómico
    if ($newStatus === 'socioeconomic_study') {
        $updateData['socioeconomic_link'] = $request->socioeconomic_link ?: null;
    }

    // 🧾 Disponibilidad
    if ($needsAvailability) {
        $updateData['availability_response']     = null;
        $updateData['availability_confirmed_at'] = null;
    }

    $application->forceFill($updateData)->save();

    $this->notifyByStatus($application, $newStatus, $request->interview_message ?: $request->message);

    return back()->with('success', 'Estado actualizado correctamente.');
}


    /**
     * 📌 Actualizar estado masivo
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'applications'             => ['required', 'array'],
            'applications.*'           => ['exists:applications,id'],
            'status'                   => ['required', 'string'],
            'interview_at'             => ['nullable', 'date', 'after:now'],
            'interview_channel'        => ['nullable', 'string', 'max:100'],
            'interview_link'           => ['nullable', 'string', 'max:255'],
            'psychotest_link'          => ['nullable', 'string', 'max:255'],
            'use_internal_psychotest'  => ['nullable'],
            'socioeconomic_link'       => ['nullable', 'string', 'max:255'],
            'message'                  => ['nullable', 'string', 'max:500'],
            'interview_message'        => ['nullable', 'string', 'max:500'],
        ]);

        $applications = Application::with('user', 'job')->whereIn('id', $request->applications)->get();
        $newStatus = $request->status;

        foreach ($applications as $application) {
            if ($application->status === 'closed') continue;

            $updateData = [
                'status'            => $newStatus,
                'status_updated_at' => now(),
            ];

            $needsAvailability = in_array($newStatus, [
                'interview_scheduled',
                'interview_deep',
                'socioeconomic_study',
            ], true);

            if (str_starts_with($newStatus, 'interview')) {
                $updateData = array_merge($updateData, [
                    'interview_at'      => $request->interview_at,
                    'interview_channel' => $request->interview_channel,
                    'interview_link'    => $request->interview_link,
                    'interview_message' => $request->interview_message,
                ]);
            }

            if ($newStatus === 'psychotest') {
                $isInternal = $request->has('use_internal_psychotest')
                    && in_array($request->input('use_internal_psychotest'), ['1', 'true', 'on'], true);

                if ($request->filled('psychotest_link')) {
                    $updateData['psychotest_link'] = $request->psychotest_link;
                } elseif ($isInternal) {
                    $updateData['psychotest_link'] = null;
                }

                $updateData['psychotest_score']         = null;
                $updateData['psychotest_answers']       = null;
                $updateData['psychotest_completed_at']  = null;
            }

            if ($newStatus === 'socioeconomic_study') {
                $updateData['socioeconomic_link'] = $request->socioeconomic_link ?: null;
            }

            if ($needsAvailability) {
                $updateData['availability_response']     = null;
                $updateData['availability_confirmed_at'] = null;
            }

            $application->forceFill($updateData)->save();

            $this->notifyByStatus($application, $newStatus, $request->interview_message ?: $request->message);
        }

        return back()->with('success', 'Estados actualizados correctamente.');
    }

    /**
     * 🔔 Notificar según estado
     */
    private function notifyByStatus(Application $application, string $status, ?string $message = null): void
    {
        try {
            switch ($status) {
                case 'shortlisted':
                    $application->user?->notify(new ApplicationShortlisted($application, $message));
                    break;
                case 'interview_scheduled':
                    $application->user?->notify(new InterviewPreliminaryScheduled($application, $message));
                    break;
                case 'interview_deep':
                    $application->user?->notify(new InterviewDeepScheduled($application, $message));
                    break;
                case 'psychotest':
                    $application->user?->notify(new PsychotestAssigned($application, $message));
                    break;
                case 'socioeconomic_study':
                    $application->user?->notify(new SocioeconomicStudyAssigned($application, $message));
                    break;
                case 'hired':
                    $application->user?->notify(new ApplicationHired($application, $message));
                    break;
                case 'rejected':
                    $application->user?->notify(new ApplicationRejected($application, $message));
                    break;
                case 'closed':
                    $application->user?->notify(new ApplicationClosed($application));
                    break;
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /**
     * 🧠 Mostrar psicométrico (usuario)
     */
    public function psychotest(Application $application)
    {
        if ($application->user_id !== Auth::id()) abort(403);
        if ($application->status !== 'psychotest') {
            abort(403, 'Esta aplicación no está en etapa de psicométrico.');
        }

        return view('user.applications.psychotest', compact('application'));
    }

    /**
     * 🔎 Panel candidato (detalle con login)
     */
    public function show(Application $application)
    {
        if ($application->user_id !== Auth::id()) abort(403);

        if (Schema::hasColumn('applications', 'read_at') && is_null($application->read_at)) {
            $application->update(['read_at' => now()]);
        }

        return view('user.applications.show', compact('application'));
    }

    /**
     * 🔗 Detalle con link firmado (solo lectura)
     */
    public function showSigned(Request $request, Application $application)
    {
        abort_unless($request->hasValidSignature(), 403);

        if (Schema::hasColumn('applications', 'read_at') && is_null($application->read_at)) {
            $application->update(['read_at' => now()]);
        }

        return view('user.applications.show', compact('application'));
    }

    /**
     * 📅 Confirmar disponibilidad (aceptar/declinar)
     */
    public function availability(Request $request, Application $application)
    {
        if ($request->isMethod('get') && !$request->hasValidSignature()) {
            abort(403, 'Link inválido o expirado.');
        }

        if ($request->isMethod('post') && (int) $application->user_id !== (int) Auth::id()) {
            abort(403);
        }

        $response = $request->get('response') ?? $request->input('response');
        if (!in_array($response, ['accepted', 'declined'], true)) {
            return back()->with('error', 'Respuesta inválida.');
        }

        $application->update([
            'availability_response'     => $response,
            'availability_confirmed_at' => now(),
        ]);

        $msg = $response === 'accepted'
            ? '¡Asistencia confirmada! Gracias.'
            : 'Se registró que no puedes asistir.';

        if ($request->isMethod('get')) {
            return view('public.availability-confirmed', [
                'application' => $application,
                'message'     => $msg,
            ]);
        }

        return back()->with('success', $msg);
    }

/**
 * 🚪 Cerrar aplicación (admin/owner de proceso)
 */
public function close(Application $application)
{
    if ($application->status === 'closed') {
        return back()->with('info', 'Esta aplicación ya estaba cerrada.');
    }

    // Actualizamos estado y limpiamos campos de disponibilidad
    $application->update([
        'status'                    => 'closed',
        'status_updated_at'         => now(),
        'availability_response'     => null,
        'availability_confirmed_at' => null,
    ]);

    try {
        // Usa el flujo centralizado de notificaciones
        $this->notifyByStatus($application, 'closed');
    } catch (\Throwable $e) {
        report($e);
    }

    return back()->with('success', 'La aplicación fue cerrada correctamente y el candidato fue notificado.');
}

    /* =========================
     | Helpers Supabase
     ========================= */
    private function supabaseHeaders(): array
    {
        $token = env('SUPABASE_SERVICE_ROLE');
        return [
            'apikey'        => $token,
            'Authorization' => 'Bearer ' . $token,
        ];
    }

    private function supabaseObjectUrl(string $bucket, string $path): string
    {
        $base  = rtrim(env('SUPABASE_URL'), '/');
        $clean = ltrim($path, '/');
        return "{$base}/storage/v1/object/{$bucket}/{$clean}";
    }
}


