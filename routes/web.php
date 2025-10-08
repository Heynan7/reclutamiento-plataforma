<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\JobController;                            // Admin (reclutador)
use App\Http\Controllers\User\JobController as UserJobController;  // Público / Usuario
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\PsychotestController;
use App\Http\Controllers\Auth\FirebaseLoginController;
use App\Http\Controllers\ReportController;

// ===============================
// 🌐 Público
// ===============================
Route::view('/', 'welcome')->name('welcome');
Route::view('/terminos', 'terms')->name('terms');

// ===============================
// 🔐 Protegidas (auth + verified)
// ===============================
Route::middleware(['auth', 'verified'])->group(function () {

    // 🧭 Redirección según rol
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if (!$user) return redirect()->route('login');

        return $user->role === 'admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('user.dashboard');
    })->name('dashboard');

    // ============================
    // 🛠️ ADMIN (reclutador)
    // ============================
    Route::middleware(['is_admin'])->prefix('admin')->name('admin.')->group(function () {

        // Dashboard
        Route::view('/dashboard', 'admin.dashboard')->name('dashboard');

        // 📂 CRUD Vacantes
        Route::resource('jobs', JobController::class);

        // Abrir / Cerrar vacante
        Route::patch('/jobs/{job}/toggle-status', [JobController::class, 'toggleStatus'])
            ->whereNumber('job')->name('jobs.toggle');

        // Restaurar (archivadas)
        Route::patch('/jobs/{id}/restore', [JobController::class, 'restore'])
            ->whereNumber('id')->name('jobs.restore');

        // Imagen desde Supabase (solo vista admin)
        Route::get('/jobs/{job}/image', [JobController::class, 'viewImage'])
            ->whereNumber('job')->name('jobs.image');

        // 👥 Postulantes de una vacante
        Route::get('/jobs/{job}/applications', [ApplicationController::class, 'byJob'])
            ->whereNumber('job')->name('jobs.applications');

        // 👁️ Detalle admin de una postulación
        Route::get('/applications/{application}', [ApplicationController::class, 'showAdmin'])
            ->whereNumber('application')->name('applications.show');

        // 🧠 Resultados psicométricos completos
        Route::get('/applications/{application}/psychotest-results', [ApplicationController::class, 'showPsychotestResults'])
            ->whereNumber('application')->name('applications.psychotestResults');

        // 📄 Descargar CV del candidato
        Route::get('/applications/{application}/cv', [ApplicationController::class, 'downloadCv'])
            ->whereNumber('application')->name('applications.downloadCv');

        // 📌 Actualizar estado (individual)
        Route::patch('/applications/{application}/status', [ApplicationController::class, 'updateStatus'])
            ->whereNumber('application')->name('applications.updateStatus');

        // 📌 Actualizar estado (masivo)
        Route::patch('/applications/bulk-update-status', [ApplicationController::class, 'bulkUpdateStatus'])
            ->name('applications.bulkUpdateStatus');

        // 📊 Reportes
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export-excel', [ReportController::class, 'exportExcel'])->name('reports.export.excel');
        Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');
    });

    // ============================
    // 👤 USER (aspirante)
    // ============================
    Route::prefix('user')->name('user.')->group(function () {

        // Dashboard
        Route::view('/dashboard', 'user.dashboard')->name('dashboard');

        // 🔎 Vacantes disponibles
        Route::get('/jobs', [UserJobController::class, 'index'])->name('jobs.index');
        Route::get('/jobs/{job}', [UserJobController::class, 'show'])
            ->whereNumber('job')->name('jobs.show');

        // 📝 Postular a vacante
        Route::post('/applications', [ApplicationController::class, 'store'])->name('applications.store');

        // 📋 Mis postulaciones
        Route::get('/applications', [ApplicationController::class, 'index'])->name('applications.index');

        // 🔎 Detalle de postulación (con login)
        Route::get('/applications/{application}', [ApplicationController::class, 'show'])
            ->whereNumber('application')->name('applications.show');

        // 📄 Descargar mi CV (desde Supabase)
        Route::get('/applications/{application}/cv', [ApplicationController::class, 'downloadCv'])
            ->whereNumber('application')->name('applications.downloadCv');

        // 🧠 Psychotest interno / externo (vista)
        Route::get('/applications/{application}/psychotest', [ApplicationController::class, 'psychotest'])
            ->whereNumber('application')->name('applications.psychotest');

        // 🧠 Enviar resultados del psicométrico (controlador dedicado)
        Route::post('/applications/{application}/psychotest', [PsychotestController::class, 'submit'])
            ->whereNumber('application')->name('applications.psychotest.submit');
    });

    // ============================
    // 🧑‍💼 PERFIL
    // ============================
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile/info', [ProfileController::class, 'updateInfo'])->name('profile.update.info');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update.password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 📅 Confirmar disponibilidad (desde panel, POST normal)
    Route::post('/applications/{application}/availability', [ApplicationController::class, 'availability'])
        ->whereNumber('application')->name('applications.availability.post');
});

// ============================
// 📅 Confirmar disponibilidad (desde correo, LINK FIRMADO 7 días)
// ============================
Route::get('/applications/{application}/availability', [ApplicationController::class, 'availability'])
    ->whereNumber('application')->middleware('signed')->name('applications.availability.get');

// ============================
// 🔗 Ver postulación con LINK FIRMADO 7 días (solo lectura, sin login)
// ============================
Route::get('/s/applications/{application}', [ApplicationController::class, 'showSigned'])
    ->whereNumber('application')->middleware('signed')->name('applications.show.signed');

// ============================
// 🖼️ Imagen pública de vacante (sin login)
// ============================
Route::get('/user/jobs/{job}/image', [UserJobController::class, 'viewImage'])
    ->whereNumber('job')->name('user.jobs.image');

// ============================
// 🔐 Login con Firebase (Google)
// ============================
Route::post('/firebase-login', [FirebaseLoginController::class, 'login'])->name('firebase.login');

// ============================
// ⚙️ Auth scaffolding (Breeze / Fortify / Jetstream)
// ============================
require __DIR__ . '/auth.php';
