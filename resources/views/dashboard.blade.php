@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
    <div class="card-body p-4">
        <h3>Bienvenido, {{ auth()->user()->name }}</h3>
        <p class="text-muted mb-4">Entraste con el rol <strong>{{ auth()->user()->role }}</strong>.</p>

        @if (auth()->user()->esAdmin())
            <div class="alert alert-danger">
                Iniciaste sesión con las <strong>3 etapas</strong>: correo y contraseña, código enviado
                al correo y verificación del código.
            </div>
            <a href="{{ route('admin.panel') }}" class="btn btn-danger">Ir al panel del administrador</a>
        @else
            <div class="alert alert-info">
                Iniciaste sesión con el proceso convencional: solo correo y contraseña.
            </div>
            <a href="{{ route('empleado.panel') }}" class="btn btn-info">Ir al panel del empleado</a>
        @endif
    </div>
</div>
@endsection
