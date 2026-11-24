@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h4 class="mb-3 text-center">Verificación en dos pasos</h4>

                <p class="text-muted text-center">
                    Enviamos un codigo de 6 dígitos a <strong>{{ $email }}</strong>.
                    El codigo vale solo <strong>1 minuto</strong>.
                </p>

                {{-- El formulario se envía a una URL temporal firmada (viene del controlador) --}}
                <form method="POST" action="{{ $urlVerificar }}">
                    @csrf

                    <div class="mb-3">
                        <label for="codigo" class="form-label">Codigo de verificación</label>
                        <input type="text" id="codigo" name="codigo" maxlength="6"
                               inputmode="numeric" autocomplete="one-time-code"
                               class="form-control text-center fs-4 @error('codigo') is-invalid @enderror"
                               required autofocus>
                        @error('codigo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-success w-100">Verificar y entrar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
