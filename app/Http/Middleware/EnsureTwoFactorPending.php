<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorPending
{
    /**
     * Deja pasar solo a quien ya validó correo y contraseña como administrador
     * (el login guarda 'pending_admin_id' en la sesión). Si alguien escribe la
     * URL de verificación directamente, no tiene esa sesión y regresa al login.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->has('pending_admin_id')) {
            return redirect()->route('login')->withErrors([
                'email' => 'Primero inicia sesión con tu correo y contraseña.',
            ]);
        }

        return $next($request);
    }
}
