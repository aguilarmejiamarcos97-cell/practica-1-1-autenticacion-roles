<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerificationCodeMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $datos = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Etapa 1: revisar correo y contraseña SIN iniciar sesión todavía
        $usuario = User::where('email', $datos['email'])->first();

        if (! $usuario || ! Hash::check($datos['password'], $usuario->password)) {
            return back()
                ->withErrors(['email' => 'Correo o contraseña incorrectos.'])
                ->onlyInput('email');
        }

        // El administrador sigue con las etapas 2 y 3 (código por correo)
        if ($usuario->esAdmin()) {
            return $this->empezarVerificacion($usuario, $request);
        }

        // El empleado entra directo
        Auth::login($usuario);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Etapa 2: genera el código, lo manda al correo y redirige a la
     * ruta temporal firmada donde se captura.
     */
    private function empezarVerificacion(User $usuario, Request $request)
    {
        $codigo = (string) random_int(100000, 999999);

        // Guardamos el código cifrado (hash), nunca en texto normal
        $usuario->forceFill([
            'verification_code' => Hash::make($codigo),
            'verification_expires_at' => now()->addMinutes(1),
            'verification_attempts' => 0,
        ])->save();

        try {
            Mail::to($usuario->email)->send(new VerificationCodeMail($usuario->name, $codigo));
        } catch (\Throwable $e) {
            report($e);

            $usuario->forceFill([
                'verification_code' => null,
                'verification_expires_at' => null,
            ])->save();

            return back()
                ->withErrors(['email' => 'No se pudo enviar el código al correo. Revisa la configuración de correo en el archivo .env.'])
                ->onlyInput('email');
        }

        // Nuevo id de sesión y marca de "ya pasó la etapa 1"
        $request->session()->regenerate();
        $request->session()->put('pending_admin_id', $usuario->id);

        // Ruta temporal: la firma deja de servir a los 1 minuto
        $urlTemporal = URL::temporarySignedRoute('verificacion.show', now()->addMinutes(1));

        return redirect($urlTemporal);
    }
}
