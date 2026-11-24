<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\TwoFactorController;
use Illuminate\Support\Facades\Route;

// La página principal manda al dashboard (si no hay sesión, el login lo recibe)
Route::redirect('/', '/dashboard');

// ---------- Login (solo para visitantes sin sesión) ----------
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])
        ->middleware('throttle:10,1')
        ->name('login.attempt');
});

// ---------- Verificación del administrador (etapa 2 y 3) ----------
// - 'pending.2fa': solo entra quien ya pasó correo y contraseña (hay una sesión pendiente)
// - 'signed': la URL debe traer una firma válida y no haber expirado (5 minutos)
// Ningún menú ni vista Blade tiene un enlace a estas rutas: solo se llega por redirección.
Route::middleware('pending.2fa')->group(function () {
    Route::get('/verificacion-segura', [TwoFactorController::class, 'show'])
        ->middleware('signed')
        ->name('verificacion.show');

    Route::post('/verificacion-segura', [TwoFactorController::class, 'verify'])
        ->middleware(['signed', 'throttle:10,1'])
        ->name('verificacion.verify');
});

// ---------- Rutas con sesión iniciada ----------
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Solo administrador
    Route::get('/admin/panel', fn () => view('panel', ['titulo' => 'Panel del Administrador']))
        ->middleware('role:admin')
        ->name('admin.panel');

    // Solo empleado
    Route::get('/empleado/panel', fn () => view('panel', ['titulo' => 'Panel del Empleado']))
        ->middleware('role:empleado')
        ->name('empleado.panel');
});
