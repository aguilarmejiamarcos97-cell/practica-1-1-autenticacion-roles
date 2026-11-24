<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Administrador: el código de verificación llega a ESTE correo.
        // Cámbialo por el correo donde quieras recibir el código.
        User::updateOrCreate(
            ['email' => 'aguilarmejiamarcos97@gmail.com'],
            [
                'name' => 'Marcos Admin',
                'password' => 'Admin12345',
                'role' => 'admin',
            ]
        );

        // Empleado: entra solo con correo y contraseña
        User::updateOrCreate(
            ['email' => 'empleado@example.com'],
            [
                'name' => 'Empleado Prueba',
                'password' => 'Empleado12345',
                'role' => 'empleado',
            ]
        );
    }
}
