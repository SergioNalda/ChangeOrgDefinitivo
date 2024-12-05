@php
    use Illuminate\Support\Facades\Auth;
@endphp
    <!DOCTYPE html>
<html lang="en">
<head>
    <title>Change.org</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand text-danger fs-2" href="{{ route('home') }}">Change.org</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link fs-4 m-2" href="{{ route('peticiones.index') }}">Peticiones</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fs-4 m-2" href="{{ route('peticiones.create') }}">Inicia una petición</a>
                </li>

                @if(Auth::check())
                    <li class="nav-item">
                        <a class="nav-link fs-4 m-2" href="{{ route('peticiones.mine') }}">Mis peticiones</a>
                    </li>

                    <!-- Enlace a las peticiones firmadas -->
                    <li class="nav-item">
                        <a class="nav-link fs-4 m-2" href="{{ route('peticiones.firmadas') }}">Mis Peticiones Firmadas</a>
                    </li>

                    <!-- Menú desplegable para el usuario autenticado -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle fs-4 m-2" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            @if(Auth::user()->profile_picture)
                                <!-- Si el usuario tiene foto de perfil, mostramos la imagen -->
                                <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Profile Picture" class="rounded-circle" width="30" height="30">
                            @else
                                <!-- Si no tiene foto, mostramos solo el nombre -->
                                {{ Auth::user()->name }}
                            @endif
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Editar Perfil</a></li>
                            <li><a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Cerrar sesión</a></li>
                        </ul>
                    </li>

                    <!-- Formulario de logout -->
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                @else
                    <!-- Enlaces de login y registro si el usuario no está autenticado -->
                    <li class="nav-item">
                        <a class="nav-link fs-5 m-2 text-danger" href="{{ route('register') }}">Registrar</a>
                    </li>
                    <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
                @endif
            </ul>
        </div>
    </div>
</nav>

@yield('content')

<footer class="bg-light text-center py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h6>Acerca de</h6>
                <ul class="list-unstyled">
                    <li><a href="#">Sobre Change.org</a></li>
                    <li><a href="#">Impacto</a></li>
                    <li><a href="#">Empleo</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h6>Comunidad</h6>
                <ul class="list-unstyled">
                    <li><a href="#">Blog</a></li>
                    <li><a href="#">Prensa</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h6>Redes sociales</h6>
                <ul class="list-unstyled">
                    <li><a href="#">Twitter</a></li>
                    <li><a href="#">Facebook</a></li>
                    <li><a href="#">Instagram</a></li>
                </ul>
            </div>
        </div>
        <p class="text-muted">© 2022 Change.org</p>
    </div>
</footer>

</body>
</html>
