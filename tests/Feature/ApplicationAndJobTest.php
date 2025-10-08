<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Job;

class ApplicationAndJobTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Prueba 1: Intentar subir un CV con extensión inválida.
     */
    public function test_upload_invalid_file_extension()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post('/applications', [
                'cv_file' => UploadedFile::fake()->create('malware.exe', 10), // ❌ inválido
                'job_id'  => Job::factory()->create()->id,
            ]);

        $response->assertSessionHasErrors('cv_file');
    }

    /**
     * Prueba 2: Subir un CV válido en PDF.
     */
    public function test_upload_valid_pdf_file()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post('/applications', [
                'cv_file' => UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf'), // ✅ válido
                'job_id'  => Job::factory()->create()->id,
            ]);

        $response->assertStatus(302); // redirige tras guardar
        $this->assertDatabaseHas('applications', [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Prueba 3: Filtro de vacantes activas (status=active).
     */
    public function test_filter_active_jobs()
    {
        Job::factory()->create(['is_open' => true]);
        Job::factory()->create(['is_open' => false]);

        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->get('/admin/jobs?status=active');

        $response->assertStatus(200);
        $response->assertSee('Vacantes'); // texto de la vista
    }

    /**
     * Prueba 4: Filtro de vacantes archivadas (status=archived).
     */
    public function test_filter_archived_jobs()
    {
        $archivedJob = Job::factory()->create();
        $archivedJob->delete(); // soft delete

        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->get('/admin/jobs?status=archived');

        $response->assertStatus(200);
        $response->assertSee('Vacantes'); // la vista debe cargar aunque no tenga vacantes
    }
}
