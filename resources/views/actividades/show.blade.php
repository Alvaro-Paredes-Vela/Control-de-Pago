@extends('adminlte::page')

@section('title', 'Detalle de actividad')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/admin-brand.css') }}?v=5">
@endpush

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h1 class="fw-bold">Detalle de actividad</h1>

        <a href="{{ route('actividades.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>
@stop

@section('content')

    <div class="row g-3">

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">

                <div class="card-body">

                    <div class="text-center mb-4">
                        <i class="bi bi-calendar-event text-primary" style="font-size: 4rem;"></i>

                        <h4 class="fw-bold mt-2">
                            {{ $actividad->nombre }}
                        </h4>

                        <span class="badge bg-primary">
                            {{ $actividad->categoria?->nombre ?? 'Sin categoría' }}
                        </span>
                    </div>

                    <p>
                        <strong>Descripción:</strong>
                        {{ $actividad->descripcion ?? 'Sin descripción' }}
                    </p>

                    <p>
                        <strong>Inicio:</strong>
                        {{ \Carbon\Carbon::parse($actividad->fecha_inicio)->format('d/m/Y H:i') }}
                    </p>

                    <p>
                        <strong>Finalización:</strong>
                        {{ $actividad->fecha_fin ? \Carbon\Carbon::parse($actividad->fecha_fin)->format('d/m/Y H:i') : 'No definida' }}
                    </p>

                    <p>
                        <strong>Lugar:</strong>
                        {{ $actividad->lugar ?? 'No definido' }}
                    </p>

                    <p>
                        <strong>Costo:</strong>
                        Bs {{ number_format($actividad->costo, 2) }}
                    </p>

                    <p>
                        <strong>Cupo máximo:</strong>
                        {{ $actividad->cupo_maximo ?? 'Sin límite' }}
                    </p>

                    <p>
                        <strong>Estado:</strong>
                        {{ ucfirst(str_replace('_', ' ', $actividad->estado)) }}
                    </p>

                    {{-- Solo el administrador puede editar actividades --}}
                    @can('administrar-actividades')
                        <a href="{{ route('actividades.edit', ['actividad' => $actividad->getRouteKey()]) }}"
                            class="btn btn-warning">
                            <i class="bi bi-pencil-square me-1"></i>
                            Editar actividad
                        </a>
                    @endcan

                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">

                <div class="card-header">
                    <h3 class="card-title">
                        <i class="bi bi-people me-2"></i>
                        Estudiantes inscritos
                    </h3>
                </div>

                <div class="card-body">

                    @php
                        $inscritos = $actividad->inscripciones->where('estado', 'inscrito');
                    @endphp

                    <p>
                        <strong>Total inscritos:</strong>
                        {{ $inscritos->count() }}
                    </p>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Registro</th>
                                    <th>Estudiante</th>
                                    <th>Costo</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($inscritos as $inscripcion)
                                    <tr>
                                        <td>
                                            {{ $inscripcion->estudiante?->registro_universitario ?? '—' }}
                                        </td>

                                        <td>
                                            {{ $inscripcion->estudiante?->nombres }}
                                            {{ $inscripcion->estudiante?->apellidos }}
                                        </td>

                                        <td>
                                            Bs {{ number_format($inscripcion->costo_acordado, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">
                                            No hay estudiantes inscritos.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($actividad->estado === 'inscripciones_abiertas')
                        <a href="{{ route('inscripciones.create') }}" class="btn btn-primary mt-3">
                            <i class="bi bi-person-plus me-1"></i>
                            Inscribir estudiante
                        </a>
                    @endif

                </div>
            </div>
        </div>

    </div>
@stop
