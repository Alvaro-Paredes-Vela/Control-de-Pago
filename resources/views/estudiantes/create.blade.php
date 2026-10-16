@extends('adminlte::page')

@section('title', 'Registrar estudiante')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/admin-brand.css') }}">
@endpush

@section('content_header')
    <h1 class="fw-bold">Registrar estudiante</h1>
@stop

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-header">
            <h3 class="card-title">
                <i class="bi bi-person-plus me-2"></i>
                Datos del estudiante
            </h3>
        </div>

        <form action="{{ route('estudiantes.store') }}" method="POST">
            @csrf

            <div class="card-body">
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Registro universitario *</label>
                        <input type="text" name="registro_universitario"
                            class="form-control @error('registro_universitario') is-invalid @enderror"
                            value="{{ old('registro_universitario') }}" maxlength="20" required>

                        @error('registro_universitario')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Celular</label>
                        <input type="text" name="celular" class="form-control @error('celular') is-invalid @enderror"
                            value="{{ old('celular') }}">
                        @error('celular')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Nombres *</label>
                        <input type="text" name="nombres" class="form-control @error('nombres') is-invalid @enderror"
                            value="{{ old('nombres') }}" required>
                        @error('nombres')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Apellidos *</label>
                        <input type="text" name="apellidos" class="form-control @error('apellidos') is-invalid @enderror"
                            value="{{ old('apellidos') }}" required>
                        @error('apellidos')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Correo electrónico</label>
                        <input type="email" name="correo" class="form-control @error('correo') is-invalid @enderror"
                            value="{{ old('correo') }}">
                        @error('correo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Semestre</label>
                        <select name="semestre" class="form-select">
                            <option value="">Seleccione un semestre</option>
                            @for ($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}" @selected(old('semestre') == $i)>
                                    {{ $i }}° semestre
                                </option>
                            @endfor
                        </select>
                    </div>

                </div>
            </div>

            <div class="card-footer d-flex justify-content-end gap-2">
                <a href="{{ route('estudiantes.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>
                    Cancelar
                </a>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>
                    Guardar estudiante
                </button>
            </div>
        </form>
    </div>
@stop
