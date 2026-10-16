@extends('adminlte::page')

@section('title', 'Editar Usuario')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/admin-brand.css') }}?v=4{{ '' }}">
@endpush

@section('content_header')
    <h1 class="fw-bold">
        <i class="bi bi-pencil-square me-2"></i>
        Editar usuario
    </h1>
@stop

@section('content')
    <div class="card border-0 shadow-sm">

        <div class="card-header">
            <h3 class="card-title">{{ $usuario->name }}</h3>
        </div>

        <form action="{{ route('usuarios.update', $usuario) }}" method="POST">
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
                        <label class="form-label">Nombre completo *</label>

                        <input type="text" name="name" class="form-control" value="{{ old('name', $usuario->name) }}"
                            required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Correo electrónico *</label>

                        <input type="email" name="email" class="form-control"
                            value="{{ old('email', $usuario->email) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Rol *</label>

                        <select name="rol" class="form-select" required>
                            <option value="administrador" @selected(old('rol', $usuario->rol) === 'administrador')>
                                Administrador
                            </option>

                            <option value="tesorero" @selected(old('rol', $usuario->rol) === 'tesorero')>
                                Tesorero
                            </option>

                            <option value="organizador" @selected(old('rol', $usuario->rol) === 'organizador')>
                                Organizador
                            </option>
                        </select>

                        @if ($usuario->id === Auth::id())
                            <div class="form-text">
                                No puedes quitarte tu propio rol de administrador.
                            </div>
                        @endif
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Nueva contraseña</label>

                        <input type="password" name="password" class="form-control" minlength="8"
                            autocomplete="new-password" placeholder="Dejar vacío para conservar la actual">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Confirmar nueva contraseña</label>

                        <input type="password" name="password_confirmation" class="form-control" minlength="8"
                            autocomplete="new-password">
                    </div>

                </div>
            </div>

            <div class="card-footer d-flex justify-content-end gap-2">
                <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
                    Cancelar
                </a>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>
                    Actualizar usuario
                </button>
            </div>
        </form>
    </div>
@stop
