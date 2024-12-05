@extends('layouts.public')

@section('content')
    <div class="container mt-4">
        <h1 class="text-center">Peticiones</h1>

        @if ($peticiones->isEmpty())
            <p>No hay peticiones disponibles.</p>
        @else
            <div class="list-group">
                @foreach($peticiones as $peticion)
                    <div class="list-group-item">
                        <h5 class="mb-1">{{ $peticion->titulo }}</h5>
                        <img src="{{ asset('peticiones/' . $peticion->files->file_path) }}" style="max-width: 20em; height: 20em;" alt="imagen peticion">
                        <p class="mb-1">{{ $peticion->descripcion }}</p>
                        <small>Estado: {{ ucfirst($peticion->estado) }}</small>
                        <br>
                        <small>Creada el: {{ $peticion->created_at->format('d/m/Y H:i') }}</small>
                        <br>

                        <!-- Botón para ver los detalles de la petición -->
                        <a href="{{ route('peticiones.show', $peticion->id) }}" class="btn btn-info mt-2">Ver Detalles</a>

                        <!-- Botón de firmar si el usuario está autenticado y no ha firmado ya -->
                        @if(Auth::check() && !$peticion->firmas->contains(Auth::user()))
                            <form action="{{ route('peticiones.firmar', $peticion->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-danger mt-2">Firmar</button>
                            </form>
                        @elseif(Auth::check() && $peticion->firmas->contains(Auth::user()))
                            <span class="badge bg-success mt-2">Ya has firmado esta petición</span>
                        @endif
                    </div>
                    <br>
                @endforeach
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-center mt-4">
                {!! $peticiones->links() !!}
            </div>
        @endif
    </div>
@endsection
