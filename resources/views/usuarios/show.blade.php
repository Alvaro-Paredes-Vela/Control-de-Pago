@extends('adminlte::page')

@section('title', 'Detalle del Usuario')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/admin-brand.css') }}?v=4{{ '' }}">
@endpush

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="fw-bold">Detalle del usuario</h1>

        <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Volver
        </a>
    </div>
@stop

@section('content')
    <div class="card border-0 shadow-sm">

        <div class="card-body text-center py-4">

            <i class="bi bi-person-circle text-primary" style="font-size: 5rem;"></i>

            <h3 class="fw-bold mt-3">
                {{ $usuario->name }}
            </h3>

            <p class="text-muted">{{ $usuario->email }}</p>

            @php
                $colorRol = match ($usuario->rol) {
                    'administrador' => 'danger',
                    'tesorero' => 'success',
                    default => 'primary',
                };
            @endphp

            <span class="badge bg-{{ $colorRol }}">
                {{ ucfirst($usuario->rol) }}
            </span>

            <hr>

            <div class="row text-start g-3">
                <div class="col-md-6">
                    <strong>Identificador:</strong>
                    <p>{{ $usuario->id }}</p>
                </div>

                <div class="col-md-6">
                    <strong>Fecha de registro:</strong>
                    <p>{{ $usuario->created_at?->format('d/m/Y H:i') }}</p>
                </div>

                <div class="col-md-6">
                    <strong>Última actualización:</strong>
                    <p>{{ $usuario->updated_at?->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            <a href="{{ route('usuarios.edit', $usuario) }}" class="btn btn-warning mt-3">
                <i class="bi bi-pencil-square me-1"></i>
                Editar usuario
            </a>

        </div>
    </div>
@stop
