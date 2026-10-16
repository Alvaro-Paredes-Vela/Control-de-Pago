@extends('adminlte::page')

@section('title', 'Editar Método de Pago')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/admin-brand.css') }}">
@endpush

@section('content_header')
    <h1 class="fw-bold">Editar método de pago</h1>
@stop

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-header">
            <h3 class="card-title">{{ $metodo->nombre }}</h3>
        </div>

        <form method="POST" action="{{ route('metodos-pago.update', $metodo) }}">
            @csrf
            @method('PUT')

            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Nombre *</label>

                    <input type="text" name="nombre" maxlength="50"
                        class="form-control @error('nombre') is-invalid @enderror"
                        value="{{ old('nombre', $metodo->nombre) }}" required>

                    @error('nombre')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="card-footer d-flex justify-content-end gap-2">
                <a href="{{ route('metodos-pago.index') }}" class="btn btn-secondary">
                    Cancelar
                </a>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>
                    Actualizar método
                </button>
            </div>
        </form>
    </div>
@stop
