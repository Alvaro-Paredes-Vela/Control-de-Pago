@extends('adminlte::page')

@section('title', 'Detalle de categoría')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/admin-brand.css') }}">
@endpush

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="fw-bold">Detalle de categoría</h1>

        <a href="{{ route('categorias.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Volver
        </a>
    </div>
@stop

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <h4 class="fw-bold">
                <i class="bi bi-tags text-primary me-2"></i>
                {{ $categoria->nombre }}
            </h4>

            <hr>

            <p>
                <strong>Descripción:</strong>
                {{ $categoria->descripcion ?? 'Sin descripción' }}
            </p>

            <p>
                <strong>Fecha de creación:</strong>
                {{ $categoria->created_at?->format('d/m/Y') ?? '—' }}
            </p>

            <a href="{{ route('categorias.edit', $categoria) }}" class="btn btn-warning">
                <i class="bi bi-pencil-square me-1"></i>
                Editar categoría
            </a>
        </div>
    </div>
@stop
