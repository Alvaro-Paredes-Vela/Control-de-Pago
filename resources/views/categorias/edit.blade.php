@extends('adminlte::page')

@section('title', 'Editar categoría')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/admin-brand.css') }}">
@endpush

@section('content_header')
    <h1 class="fw-bold">Editar categoría</h1>
@stop

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-header">
            <h3 class="card-title">{{ $categoria->nombre }}</h3>
        </div>

        <form method="POST" action="{{ route('categorias.update', $categoria) }}">
            @csrf
            @method('PUT')

            <div class="card-body">

                <div class="mb-3">
                    <label class="form-label">Nombre *</label>

                    <input type="text" name="nombre" value="{{ old('nombre', $categoria->nombre) }}"
                        class="form-control @error('nombre') is-invalid @enderror" required>

                    @error('nombre')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Descripción</label>

                    <textarea name="descripcion" rows="3" class="form-control @error('descripcion') is-invalid @enderror">{{ old('descripcion', $categoria->descripcion) }}</textarea>

                    @error('descripcion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

            </div>

            <div class="card-footer d-flex justify-content-end gap-2">
                <a href="{{ route('categorias.index') }}" class="btn btn-secondary">
                    Cancelar
                </a>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>
                    Actualizar categoría
                </button>
            </div>
        </form>
    </div>
@stop
