<?php

use App\Http\Middleware\CheckRole;
use App\Http\Middleware\EnsureTwoFactorPending;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Exceptions\InvalidSignatureException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Nombres cortos para usar nuestros middlewares en las rutas
        $middleware->alias([
            'role' => CheckRole::class,
            'pending.2fa' => EnsureTwoFactorPending::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Si el enlace temporal de verificación expiró o fue modificado,
        // regresamos al login con un mensaje en vez de mostrar el error 403.
        $exceptions->render(function (InvalidSignatureException $e, Request $request) {
            return redirect()->route('login')->withErrors([
                'email' => 'El enlace de verificación expiró o no es válido. Inicia sesión de nuevo.',
            ]);
        });
    })->create();
