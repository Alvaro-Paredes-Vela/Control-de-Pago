@extends('adminlte::page')

@section('title', 'Nuevo Usuario')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/admin-brand.css') }}?v=4{{ '' }}">
@endpush

@section('content_header')
    <h1 class="fw-bold">
        <i class="bi bi-person-plus me-2"></i>
        Registrar usuario
    </h1>
@stop

@section('content')
    <div class="card border-0 shadow-sm">

        <div class="card-header">
            <h3 class="card-title">Datos de acceso</h3>
        </div>

        <form action="{{ route('usuarios.store') }}" method="POST">
            @csrf

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
                        <label class="form-label">Nombre completo *</label>

                        <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                            maxlength="255" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Correo electrónico *</label>

                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Contraseña *</label>

                        <input type="password" name="password" class="form-control" minlength="8"
                            autocomplete="new-password" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Confirmar contraseña *</label>

                        <input type="password" name="password_confirmation" class="form-control" minlength="8"
                            autocomplete="new-password" required>
                    </div>

                    <div class="col-12">
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle me-1"></i>
                            El usuario tendrá inicialmente el rol
                            <strong>Organizador</strong>.
                            El administrador podrá modificarlo después.
                        </div>
                    </div>

                </div>
            </div>

            <div class="card-footer d-flex justify-content-end gap-2">
                <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
                    Cancelar
                </a>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>
                    Guardar usuario
                </button>
            </div>
        </form>
    </div>
@stop
