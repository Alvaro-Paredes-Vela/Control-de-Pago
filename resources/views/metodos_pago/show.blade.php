@extends('adminlte::page')

@section('title', 'Detalle del Método de Pago')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/admin-brand.css') }}">
@endpush

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="fw-bold">Detalle del método de pago</h1>

        <a href="{{ route('metodos-pago.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>
@stop

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-4">

            <i class="bi bi-credit-card text-primary" style="font-size: 4rem;"></i>

            <h3 class="fw-bold mt-3">{{ $metodo->nombre }}</h3>

            <p class="text-muted">
                Fecha de creación:
                {{ $metodo->created_at?->format('d/m/Y') ?? '—' }}
            </p>

            <a href="{{ route('metodos-pago.edit', $metodo) }}" class="btn btn-warning">
                <i class="bi bi-pencil-square me-1"></i>
                Editar método
            </a>
        </div>
    </div>
@stop
