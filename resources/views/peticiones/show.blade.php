@extends('layouts.public')

@section('content')
    <div class="container mt-4">
        <h1>{{ $peticion->titulo }}</h1>
        <img src="{{ asset('peticiones/' . $peticion->files->file_path) }}" style="max-width: 20em; height: 20em;" alt="imagen peticion">
        <p>{{ $peticion->descripcion }}</p>
        <p><strong>Destinatario:</strong> {{ $peticion->destinatario }}</p>
        <p><strong>Estado:</strong> {{ ucfirst($peticion->estado) }}</p>
        <p><strong>Categoría:</strong> {{ $peticion->categoria->nombre }}</p>
        <p><strong>Creada el:</strong> {{ $peticion->created_at->format('d/m/Y H:i') }}</p>

        <!-- Mostrar el número de firmas -->
        <p><strong>Firmas:</strong> {{ $peticion->firmas()->count() }} personas han firmado esta petición.</p>

        <!-- Botón para firmar si el usuario está autenticado y no ha firmado aún -->
        @if(Auth::check() && !$peticion->firmas->contains(Auth::user()))
            <form action="{{ route('peticiones.firmar', $peticion->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-danger">Firmar</button>
            </form>
        @elseif(Auth::check() && $peticion->firmas->contains(Auth::user()))
            <span class="badge bg-success">Ya has firmado esta petición</span>
        @endif

        <div class="mt-4">
            <a href="{{ route('peticiones.index') }}" class="btn btn-danger">Volver al listado</a>
        </div>
    </div>
@endsection
