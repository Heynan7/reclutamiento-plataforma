<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\User;

use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;
use Lcobucci\JWT\Token\InvalidTokenStructure;
use Lcobucci\JWT\Token\InvalidToken;
use Throwable;

class FirebaseLoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate(['idToken' => 'required|string']);

        try {
            // 0) Comprobar que existe el JSON de credenciales
            $credPath = config('firebase.projects.app.credentials') // ruta efectiva
                ?? config('firebase.credentials');                  // compatibilidad
            if (!$credPath || !file_exists($credPath)) {
                throw new \RuntimeException("FIREBASE_CREDENTIALS no apunta a un archivo válido: {$credPath}");
            }

            /** @var \Kreait\Firebase\Auth $firebaseAuth */
            $firebaseAuth = app('firebase.auth');

            // 1) Verificar token de Firebase
            $verifiedToken = $firebaseAuth->verifyIdToken($request->idToken);
            $uid = $verifiedToken->claims()->get('sub');

            // 2) Obtener perfil del usuario en Firebase
            $fbUser = $firebaseAuth->getUser($uid);
            $email  = $fbUser->email ?? null;
            $name   = $fbUser->displayName ?? ($email ? explode('@', $email)[0] : 'Usuario');
            $avatar = $fbUser->photoUrl ?? null;

            if (!$email) {
                throw new \RuntimeException(
                    'El token verificado no contiene email (activa y permite email en el proveedor Google de Firebase Auth).'
                );
            }

            // 3) Crear o actualizar usuario en tu BD (rol por defecto = user)
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name'              => $name,
                    'password'          => bcrypt(Str::random(32)), // contraseña aleatoria (no usada en Google)
                    'role'              => 'user',
                    'avatar'            => $avatar,
                    'provider'          => 'google', // ✅ marcamos como usuario Google
                    'email_verified_at' => now(),
                ]
            );

            // 4) Iniciar sesión en Laravel
            Auth::login($user, remember: true);

            // 5) Redirigir según rol
            $redirect = $user->role === 'admin'
                ? route('admin.dashboard')
                : route('user.dashboard');

            return response()->json(['redirect' => $redirect]);

        } catch (FailedToVerifyToken $e) {
            Log::error('[FirebaseLogin] FailedToVerifyToken: '.$e->getMessage());
            $msg = 'Error al validar el ID token de Google. Posibles causas: '
                 . 'reloj del sistema desincronizado, token expirado/revocado, '
                 . 'o el token no pertenece al mismo proyecto Firebase.';
            return response()->json([
                'message' => config('app.debug') ? "FailedToVerifyToken: ".$e->getMessage() : $msg
            ], 401);

        } catch (InvalidTokenStructure|InvalidToken $e) {
            Log::error('[FirebaseLogin] InvalidToken: '.$e->getMessage());
            return response()->json([
                'message' => config('app.debug') ? "InvalidToken: ".$e->getMessage() : 'ID token inválido.'
            ], 401);

        } catch (Throwable $e) {
            Log::error('[FirebaseLogin] '.$e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            $msg = config('app.debug')
                ? ('Error al validar login con Google: '.$e->getMessage())
                : 'Error al validar login con Google';

            return response()->json(['message' => $msg], 401);
        }
    }
}
