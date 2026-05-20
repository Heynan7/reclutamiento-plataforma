<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{
    /**
     * Mostrar el formulario de registro.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Procesar el registro del usuario.
     */
    public function store(Request $request): RedirectResponse
    {
        // Si aceptas solo 8 dígitos exactos:
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'phone'    => ['required', 'regex:/^[0-9]{8}$/'], // solo 8 dígitos
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // (Opcional) asegurar email en minúsculas
        $data['email'] = Str::lower($data['email']);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'],
            'password' => Hash::make($data['password']),
            'role'     => 'user',               // ajusta si tu default no es admin
            'cv_file'  => null,
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect()
            ->route('verification.notice')
            ->with('status', 'Te enviamos un correo de verificación. Revisa tu bandeja de entrada 📩.');
    }
}
