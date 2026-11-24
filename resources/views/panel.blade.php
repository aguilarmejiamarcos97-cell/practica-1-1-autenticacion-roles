@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
    <div class="card-body p-4">
        <h3>{{ $titulo }}</h3>
        <p class="text-muted">
            Esta sección está protegida con el middleware <code>role</code>: si tu rol no corresponde,
            el servidor responde 403 aunque escribas la URL a mano.
        </p>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">Volver al dashboard</a>
    </div>
</div>
@endsection
