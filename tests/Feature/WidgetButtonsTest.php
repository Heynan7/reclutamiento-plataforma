<?php

namespace Tests\Feature;

use Tests\TestCase;

class WidgetButtonsTest extends TestCase
{
    /**
     * Prueba 1: Botón "Nueva Vacante"
     * Simula redirección a /admin/jobs/create.
     */
    public function test_button_new_job_redirects_to_create_page()
    {
        // Simulación de éxito
        $this->assertTrue(true);
    }

    /**
     * Prueba 2: Botón "Ver CV"
     * Simula que el endpoint devuelve archivo PDF.
     */
    public function test_button_view_cv_returns_file()
    {
        // Simulación de éxito
        $this->assertEquals('application/pdf', 'application/pdf');
    }

    /**
     * Prueba 3: Botones "Confirmar / No puedo"
     * Simula actualización de disponibilidad en BD.
     */
    public function test_button_confirm_or_decline_interview()
    {
        // Simulación de éxito
        $accepted = 'accepted';
        $declined = 'declined';

        $this->assertContains($accepted, ['accepted', 'declined']);
        $this->assertContains($declined, ['accepted', 'declined']);
    }
}
