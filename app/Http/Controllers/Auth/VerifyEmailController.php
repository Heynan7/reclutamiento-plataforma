<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Marcar el correo del usuario autenticado como verificado.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        // Si ya estaba verificado, redirige directo al dashboard
        if ($request->user()->hasVerifiedEmail()) {
            return $this->redirectByRole(
                $request->user()->role,
                'Tu correo ya estaba verificado.'
            );
        }

        // Marcar como verificado y lanzar evento
        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        // Redirigir al dashboard correcto
        return $this->redirectByRole(
            $request->user()->role,
            'Correo verificado correctamente. ¡Bienvenido!'
        );
    }

    /**
     * Redirigir al dashboard según el rol.
     */
    private function redirectByRole(string $role, string $message): RedirectResponse
    {
        $redirect = $role === 'admin'
            ? route('admin.dashboard')
            : route('user.dashboard');

        return redirect($redirect)->with('status', $message);
    }
}
