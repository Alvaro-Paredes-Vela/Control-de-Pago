@extends('adminlte::page')

@section('title', 'Usuarios')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/admin-brand.css') }}?v=4{{ '' }}">
@endpush

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1 class="fw-bold">Usuarios del Sistema</h1>
            <p class="text-muted mb-0">
                Administración de cuentas y roles
            </p>
        </div>

        <a href="{{ route('usuarios.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus me-1"></i>
            Nuevo usuario
        </a>
    </div>
@stop

@section('content')

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header">
            <h3 class="card-title">
                <i class="bi bi-people me-2"></i>
                Lista de usuarios
            </h3>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th>Correo electrónico</th>
                            <th>Rol</th>
                            <th>Fecha de registro</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($usuarios as $usuario)
                            <tr>
                                <td>
                                    {{ $usuarios->firstItem() + $loop->index }}
                                </td>

                                <td>
                                    <i class="bi bi-person-circle text-primary me-2"></i>
                                    <strong>{{ $usuario->name }}</strong>

                                    @if ($usuario->id === Auth::id())
                                        <span class="badge bg-info">Tú</span>
                                    @endif
                                </td>

                                <td>{{ $usuario->email }}</td>

                                <td>
                                    @php
                                        $colores = [
                                            'administrador' => 'danger',
                                            'tesorero' => 'success',
                                            'organizador' => 'primary',
                                        ];
                                    @endphp

                                    <span class="badge bg-{{ $colores[$usuario->rol] ?? 'secondary' }}">
                                        {{ ucfirst($usuario->rol) }}
                                    </span>
                                </td>

                                <td>
                                    {{ $usuario->created_at?->format('d/m/Y') ?? '—' }}
                                </td>

                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('usuarios.show', $usuario) }}"
                                            class="btn btn-info btn-sm text-white" title="Ver">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        <a href="{{ route('usuarios.edit', $usuario) }}" class="btn btn-warning btn-sm"
                                            title="Editar">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        @if ($usuario->id !== Auth::id())
                                            <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST"
                                                onsubmit="return confirm('¿Solicitar eliminación del usuario?')">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="btn btn-outline-danger btn-sm"
                                                    title="Administrar eliminación">
                                                    <i class="bi bi-person-x"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    No se encontraron usuarios.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $usuarios->links() }}
            </div>
        </div>
    </div>
@stop
