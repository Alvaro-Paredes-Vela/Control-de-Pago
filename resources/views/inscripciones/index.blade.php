{{-- resources/views/inscripciones/index.blade.php --}}

@extends('adminlte::page')

@section('title', 'Inscripciones')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/admin-brand.css') }}">

    <style>
        .inscripcion-card {
            border: 0;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, .07);
        }

        .table td,
        .table th {
            vertical-align: middle;
        }

        .action-buttons {
            display: flex;
            gap: 6px;
            justify-content: center;
        }
    </style>
@endpush

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1 class="fw-bold">Inscripciones</h1>
            <p class="text-muted mb-0">
                Control de participantes de las actividades
            </p>
        </div>

        <a href="{{ route('inscripciones.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus me-1"></i>
            Nueva inscripción
        </a>
    </div>
@stop

@section('content')

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card inscripcion-card">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">
                    <i class="bi bi-journal-check me-2"></i>
                    Lista de inscripciones
                </h5>

                <span class="badge bg-primary">
                    {{ $inscripciones->total() }} registros
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Registro</th>
                            <th>Estudiante</th>
                            <th>Actividad</th>
                            <th>Fecha</th>
                            <th>Costo</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($inscripciones as $inscripcion)
                            <tr>
                                <td>
                                    {{ $inscripciones->firstItem() + $loop->index }}
                                </td>

                                <td>
                                    <strong>
                                        {{ $inscripcion->estudiante?->registro_universitario ?? '—' }}
                                    </strong>
                                </td>

                                <td>
                                    {{ $inscripcion->estudiante?->nombres }}
                                    {{ $inscripcion->estudiante?->apellidos }}
                                </td>

                                <td>
                                    {{ $inscripcion->actividad?->nombre ?? '—' }}
                                </td>

                                <td>
                                    {{ $inscripcion->created_at?->format('d/m/Y') ?? '—' }}
                                </td>

                                <td>
                                    Bs {{ number_format($inscripcion->costo_acordado, 2) }}
                                </td>

                                <td>
                                    @if ($inscripcion->estado === 'inscrito')
                                        <span class="badge bg-success">
                                            Inscrito
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            Cancelado
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('inscripciones.show', $inscripcion) }}"
                                            class="btn btn-info btn-sm text-white" title="Ver inscripción">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        <a href="{{ route('inscripciones.edit', $inscripcion) }}"
                                            class="btn btn-warning btn-sm" title="Editar">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        @if ($inscripcion->estado === 'inscrito')
                                            <form action="{{ route('inscripciones.destroy', $inscripcion) }}"
                                                method="POST" onsubmit="return confirm('¿Cancelar esta inscripción?')">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    title="Cancelar inscripción">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    <i class="bi bi-journal-x fs-2 d-block mb-2"></i>
                                    No existen inscripciones registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $inscripciones->links() }}
            </div>

        </div>
    </div>
@stop
