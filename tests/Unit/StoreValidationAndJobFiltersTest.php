<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Job;
use App\Models\User;

class StoreValidationAndJobFiltersTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Prueba 1: Rechaza archivo con extensión inválida (mimes:pdf).
     */
    public function test_rejects_invalid_cv_extension()
    {
        $file = UploadedFile::fake()->create('malware.exe', 10, 'application/octet-stream');

        $data = ['cv_file' => $file];

        $rules = ['cv_file' => 'required|mimes:pdf|max:5120'];

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails(), 'Se esperaba fallo de validación para extensión inválida.');
        $this->assertArrayHasKey('cv_file', $validator->failed());
    }

    /**
     * Prueba 2: Acepta PDF válido.
     */
    public function test_accepts_valid_pdf_cv()
    {
        $file = UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf');

        $data = ['cv_file' => $file];

        $rules = ['cv_file' => 'required|mimes:pdf|max:5120'];

        $validator = Validator::make($data, $rules);

        $this->assertFalse($validator->fails(), 'No se esperaba fallo de validación para PDF válido.');
    }

    /**
     * Prueba 3: Filtrar vacantes activas.
     */
    public function test_filter_active_jobs()
    {
        // Crea jobs: 1 activo, 1 cerrado
        Job::factory()->create(['is_open' => true]);
        Job::factory()->create(['is_open' => false]);

        $active = Job::where('is_open', true)->get();

        $this->assertCount(1, $active);
    }

    /**
     * Prueba 4: Vacantes archivadas (soft deletes).
     */
    public function test_filter_archived_jobs()
    {
        $job = Job::factory()->create();
        $job->delete(); // soft delete

        $archived = Job::onlyTrashed()->get();

        $this->assertGreaterThanOrEqual(1, $archived->count());
    }
}
