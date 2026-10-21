@extends('adminlte::page')

@section('title', 'Eliminar usuario')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/admin-brand.css') }}?v=4{{ '' }}">
@endpush

@section('content_header')
    <div>
        <h1 class="fw-bold">Eliminar usuario</h1>
        <p class="text-muted mb-0">Revisa los datos antes de confirmar</p>
    </div>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-7">

            <div class="card border-0 shadow-sm border-top border-danger border-3">
                <div class="card-header">
                    <h3 class="card-title text-danger">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        Vas a eliminar esta cuenta
                    </h3>
                </div>

                <div class="card-body">

                    {{-- Datos del usuario --}}
                    <div class="d-flex align-items-center mb-4">
                        <i class="bi bi-person-circle text-danger me-3" style="font-size: 3rem;"></i>
                        <div>
                            <h4 class="mb-0 fw-bold">{{ $usuario->name }}</h4>
                            <span class="text-muted">{{ $usuario->email }}</span>
                        </div>
                    </div>

                    @php
                        $colores = [
                            'administrador' => 'danger',
                            'tesorero' => 'success',
                            'organizador' => 'primary',
                        ];
                    @endphp

                    <ul class="list-group list-group-flush mb-4">
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Rol</span>
                            <span class="badge bg-{{ $colores[$usuario->rol] ?? 'secondary' }}">
                                {{ ucfirst($usuario->rol) }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Estado</span>
                            @if ($usuario->aprobado)
                                <span class="badge text-bg-success">Aprobado</span>
                            @else
                                <span class="badge text-bg-warning">Pendiente</span>
                            @endif
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Fecha de registro</span>
                            <span>{{ $usuario->created_at?->format('d/m/Y H:i') ?? '—' }}</span>
                        </li>
                    </ul>

                    {{-- Advertencia --}}
                    <div class="alert alert-danger">
                        <i class="bi bi-shield-exclamation me-1"></i>
                        Esta acción es <strong>permanente</strong>: la cuenta se borrará por completo
                        y esta persona perderá el acceso al sistema de inmediato. No se puede deshacer.
                    </div>

                    {{-- Confirmación --}}
                    <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST" novalidate>
                        @csrf
                        @method('DELETE')

                        <label for="correo_confirmacion" class="form-label">
                            Para confirmar, escribe el correo
                            <strong>{{ $usuario->email }}</strong>
                        </label>

                        <input type="text" name="correo_confirmacion" id="correo_confirmacion"
                            class="form-control mb-1 @error('correo_confirmacion') is-invalid @enderror"
                            placeholder="{{ $usuario->email }}" autocomplete="off" autofocus>

                        @error('correo_confirmacion')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>
                                Cancelar
                            </a>

                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash3-fill me-1"></i>
                                Eliminar definitivamente
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
@stop
