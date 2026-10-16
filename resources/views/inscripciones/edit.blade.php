{{-- resources/views/inscripciones/edit.blade.php --}}

@extends('adminlte::page')

@section('title', 'Editar inscripción')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/admin-brand.css') }}">
@endpush

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h1 class="fw-bold">
            <i class="bi bi-pencil-square me-2"></i>
            Editar inscripción
        </h1>

        <a href="{{ route('inscripciones.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Volver
        </a>
    </div>
@stop

@section('content')

    <div class="card border-0 shadow-sm">
        <div class="card-header">
            <h3 class="card-title">
                Inscripción #{{ $inscripcion->id }}
            </h3>
        </div>

        <form action="{{ route('inscripciones.update', $inscripcion) }}" method="POST">

            @csrf
            @method('PUT')

            <div class="card-body">

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Estudiante</label>

                        <input type="text" class="form-control"
                            value="{{ $inscripcion->estudiante?->nombres }} {{ $inscripcion->estudiante?->apellidos }}"
                            readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Registro universitario</label>

                        <input type="text" class="form-control"
                            value="{{ $inscripcion->estudiante?->registro_universitario }}" readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Actividad</label>

                        <input type="text" class="form-control" value="{{ $inscripcion->actividad?->nombre }}" readonly>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Costo acordado</label>

                        <div class="input-group">
                            <span class="input-group-text">Bs</span>

                            <input type="text" class="form-control"
                                value="{{ number_format($inscripcion->costo_acordado, 2, '.', '') }}" readonly>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Estado</label>

                        <input type="text" class="form-control" value="{{ ucfirst($inscripcion->estado) }}" readonly>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Observaciones</label>

                        <textarea name="observaciones" rows="4" class="form-control @error('observaciones') is-invalid @enderror"
                            placeholder="Escribe una observación">{{ old('observaciones', $inscripcion->observaciones) }}</textarea>

                        @error('observaciones')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>

            <div class="card-footer d-flex justify-content-end gap-2">
                <a href="{{ route('inscripciones.index') }}" class="btn btn-secondary">
                    Cancelar
                </a>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>
                    Actualizar inscripción
                </button>
            </div>
        </form>
    </div>
@stop
