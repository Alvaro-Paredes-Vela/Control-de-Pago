@extends('adminlte::page')

@section('title', 'Nueva categoría')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/admin-brand.css') }}">
@endpush

@section('content_header')
    <h1 class="fw-bold">Registrar categoría</h1>
@stop

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-header">
            <h3 class="card-title">Datos de la categoría</h3>
        </div>

        <form method="POST" action="{{ route('categorias.store') }}">
            @csrf

            <div class="card-body">

                <div class="mb-3">
                    <label class="form-label">Nombre *</label>

                    <input type="text" name="nombre" value="{{ old('nombre') }}"
                        class="form-control @error('nombre') is-invalid @enderror" placeholder="Ej. Conferencias" required>

                    @error('nombre')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Descripción</label>

                    <textarea name="descripcion" rows="3" class="form-control @error('descripcion') is-invalid @enderror"
                        placeholder="Descripción de la categoría">{{ old('descripcion') }}</textarea>

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
                    <i class="bi bi-save me-1"></i>
                    Guardar categoría
                </button>
            </div>
        </form>
    </div>
@stop
