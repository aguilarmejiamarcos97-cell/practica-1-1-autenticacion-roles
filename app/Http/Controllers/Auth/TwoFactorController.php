<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;

class TwoFactorController extends Controller
{
    // Máximo de códigos incorrectos antes de invalidar el código
    private const MAX_INTENTOS = 5;

    /**
     * Muestra el formulario donde se escribe el código.
     */
    public function show(Request $request)
    {
        $usuario = User::find($request->session()->get('pending_admin_id'));

        if (! $usuario || ! $usuario->verification_expires_at || now()->greaterThan($usuario->verification_expires_at)) {
            return $this->cancelar($request, $usuario, 'El código expiró. Inicia sesión de nuevo.');
        }

        // El formulario también se envía a una URL temporal firmada
        $urlVerificar = URL::temporarySignedRoute('verificacion.verify', $usuario->verification_expires_at);

        return view('auth.verify-code', [
            'email' => $usuario->email,
            'urlVerificar' => $urlVerificar,
        ]);
    }

    /**
     * Etapa 3: revisa el código y, si es correcto, inicia la sesión.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'codigo' => ['required', 'digits:6'],
        ]);

        $usuario = User::find($request->session()->get('pending_admin_id'));

        if (! $usuario || ! $usuario->verification_code || ! $usuario->verification_expires_at) {
            return $this->cancelar($request, $usuario, 'No hay un código activo. Inicia sesión de nuevo.');
        }

        // El código solo vale 5 minutos
        if (now()->greaterThan($usuario->verification_expires_at)) {
            return $this->cancelar($request, $usuario, 'El código expiró. Inicia sesión de nuevo.');
        }

        // Código incorrecto: contamos el intento
        if (! Hash::check($request->input('codigo'), $usuario->verification_code)) {
            $usuario->increment('verification_attempts');

            if ($usuario->verification_attempts >= self::MAX_INTENTOS) {
                return $this->cancelar($request, $usuario, 'Demasiados intentos fallidos. Inicia sesión de nuevo.');
            }

            return back()->withErrors(['codigo' => 'El código es incorrecto.']);
        }

        // Código correcto: se borra y ahora sí se inicia la sesión
        $usuario->forceFill([
            'verification_code' => null,
            'verification_expires_at' => null,
            'verification_attempts' => 0,
        ])->save();

        $request->session()->forget('pending_admin_id');

        Auth::login($usuario);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    /**
     * Invalida el código y regresa al login con un mensaje.
     */
    private function cancelar(Request $request, ?User $usuario, string $mensaje)
    {
        if ($usuario) {
            $usuario->forceFill([
                'verification_code' => null,
                'verification_expires_at' => null,
                'verification_attempts' => 0,
            ])->save();
        }

        $request->session()->forget('pending_admin_id');

        return redirect()->route('login')->withErrors(['email' => $mensaje]);
    }
}
