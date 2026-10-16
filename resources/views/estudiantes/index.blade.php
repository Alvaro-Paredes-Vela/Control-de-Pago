@extends('adminlte::page')

@section('title', 'Estudiantes')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/admin-brand.css') }}">
    <style>
        .student-card {
            border: 0;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, .06);
        }

        .table td,
        .table th {
            vertical-align: middle;
        }

        .action-buttons {
            display: flex;
            gap: 6px;
        }
    </style>
@endpush

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1 class="fw-bold">Estudiantes</h1>
            <p class="text-muted mb-0">
                Administración de estudiantes de Ingeniería en Sistemas
            </p>
        </div>

        <a href="{{ route('estudiantes.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus me-1"></i>
            Nuevo estudiante
        </a>
    </div>
@stop

@section('content')
    <div class="card student-card">
        <div class="card-body">

            {{-- BUSCADOR --}}
            <form method="GET" action="{{ route('estudiantes.index') }}" class="row g-2 mb-4">

                <div class="col-md-10">
                    <input type="text" name="buscar" class="form-control" value="{{ request('buscar') }}"
                        placeholder="Buscar por registro, nombre o apellido">
                </div>

                <div class="col-md-2">
                    <button class="btn btn-primary w-100" type="submit">
                        <i class="bi bi-search"></i> Buscar
                    </button>
                </div>
            </form>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            {{-- TABLA --}}
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Registro</th>
                            <th>Nombre completo</th>
                            <th>Celular</th>
                            <th>Semestre</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($estudiantes as $estudiante)
                            <tr>
                                <td>
                                    {{ $estudiantes->firstItem() + $loop->index }}
                                </td>
                                <td>
                                    <strong>
                                        {{ $estudiante->registro_universitario }}
                                    </strong>
                                </td>
                                <td>
                                    {{ $estudiante->nombres }}
                                    {{ $estudiante->apellidos }}
                                </td>
                                <td>{{ $estudiante->celular ?? '—' }}</td>
                                <td>{{ $estudiante->semestre ?? '—' }}</td>
                                <td>
                                    @if ($estudiante->estado === 'activo')
                                        <span class="badge bg-success">Activo</span>
                                    @else
                                        <span class="badge bg-secondary">Inactivo</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-buttons justify-content-center">
                                        <a href="{{ route('estudiantes.show', $estudiante) }}"
                                            class="btn btn-info btn-sm text-white" title="Ver">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        <a href="{{ route('estudiantes.edit', $estudiante) }}"
                                            class="btn btn-warning btn-sm" title="Editar">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        @if ($estudiante->estado === 'activo')
                                            <form action="{{ route('estudiantes.destroy', $estudiante) }}" method="POST"
                                                onsubmit="return confirm('¿Desactivar este estudiante?')">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="btn btn-danger btn-sm" title="Desactivar">
                                                    <i class="bi bi-person-x"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="bi bi-people fs-2 d-block mb-2"></i>
                                    No se encontraron estudiantes.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $estudiantes->links() }}
            </div>
        </div>
    </div>
@stop
