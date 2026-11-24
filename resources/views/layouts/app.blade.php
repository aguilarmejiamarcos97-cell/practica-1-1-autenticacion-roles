<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Práctica 1.1 - Autenticación por rol</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <span class="navbar-brand">Práctica 1.1 · Autenticación por rol</span>

            @auth
                <div class="d-flex align-items-center gap-3">
                    <span class="text-white">
                        {{ auth()->user()->name }}
                        <span class="badge {{ auth()->user()->esAdmin() ? 'bg-danger' : 'bg-info text-dark' }}">
                            {{ auth()->user()->role }}
                        </span>
                    </span>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm">Cerrar sesión</button>
                    </form>
                </div>
            @endauth
        </div>
    </nav>

    <main class="container py-5">
        @yield('content')
    </main>

</body>
</html>
