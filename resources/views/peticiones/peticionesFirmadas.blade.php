@extends('layouts.public')

@section('content')
    <div class="container mt-4">
        <h1 class="text-center">Peticiones Firmadas</h1>

        @if ($peticiones->isEmpty())
            <p>No has firmado ninguna petición aún.</p>
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

                        <!-- Botón para ver detalles de la petición -->
                        <a href="{{ route('peticiones.show', $peticion->id) }}" class="btn btn-danger mt-2">Ver Detalles</a>
                    </div>
                    <br>
                @endforeach
            </div>
        @endif
    </div>
@endsection

