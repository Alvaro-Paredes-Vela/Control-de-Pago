@extends('adminlte::page')

@section('title', 'Actividades')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/admin-brand.css') }}">
    <style>
        .activity-card {
            border: 0;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, .07);
        }

        .activity-table td,
        .activity-table th {
            vertical-align: middle;
        }

        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 6px;
        }
    </style>
@endpush

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1 class="fw-bold">Actividades</h1>
            <p class="text-muted mb-0">
                Gestión de conferencias, talleres, torneos y paseos
            </p>
        </div>

        <a href="{{ route('actividades.create') }}" class="btn btn-primary">
            <i class="bi bi-calendar-plus me-1"></i>
            Nueva actividad
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

    <div class="card activity-card">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">
                    <i class="bi bi-calendar-event me-2"></i>
                    Lista de actividades
                </h5>

                <span class="badge bg-primary">
                    {{ $actividades->total() }} registradas
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-striped activity-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Actividad</th>
                            <th>Categoría</th>
                            <th>Fecha</th>
                            <th>Costo</th>
                            <th>Cupo</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($actividades as $actividad)
                            <tr>
                                <td>
                                    {{ $actividades->firstItem() + $loop->index }}
                                </td>

                                <td>
                                    <strong>{{ $actividad->nombre }}</strong>
                                    <div class="text-muted small">
                                        {{ $actividad->lugar ?? 'Sin lugar definido' }}
                                    </div>
                                </td>

                                <td>
                                    {{ $actividad->categoria?->nombre ?? 'Sin categoría' }}
                                </td>

                                <td>
                                    {{ \Carbon\Carbon::parse($actividad->fecha_inicio)->format('d/m/Y H:i') }}
                                </td>

                                <td>
                                    Bs {{ number_format($actividad->costo, 2) }}
                                </td>

                                <td>
                                    {{ $actividad->cupo_maximo ?? 'Sin límite' }}
                                </td>

                                <td>
                                    @php
                                        $colores = [
                                            'planificada' => 'secondary',
                                            'inscripciones_abiertas' => 'success',
                                            'finalizada' => 'primary',
                                            'cancelada' => 'danger',
                                        ];
                                    @endphp

                                    <span class="badge bg-{{ $colores[$actividad->estado] ?? 'secondary' }}">
                                        {{ str_replace('_', ' ', ucfirst($actividad->estado)) }}
                                    </span>
                                </td>

                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('actividades.show', $actividad) }}"
                                            class="btn btn-info btn-sm text-white" title="Ver detalles">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        <a href="{{ route('actividades.edit', $actividad) }}"
                                            class="btn btn-warning btn-sm" title="Editar">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <form action="{{ route('actividades.destroy', $actividad) }}" method="POST"
                                            onsubmit="return confirm('¿Eliminar esta actividad?')">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    <i class="bi bi-calendar-x fs-2 d-block mb-2"></i>
                                    No existen actividades registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $actividades->links() }}
            </div>

        </div>
    </div>
@stop
