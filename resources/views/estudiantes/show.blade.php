@extends('adminlte::page')

@section('title', 'Detalle del estudiante')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/admin-brand.css') }}">
@endpush

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="fw-bold">Detalle del estudiante</h1>

        <a href="{{ route('estudiantes.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Volver
        </a>
    </div>
@stop

@section('content')
    <div class="row g-3">

        {{-- DATOS DEL ESTUDIANTE --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">

                    <i class="bi bi-person-circle text-primary" style="font-size: 5rem;"></i>

                    <h4 class="fw-bold mt-3">
                        {{ $estudiante->nombres }}
                        {{ $estudiante->apellidos }}
                    </h4>

                    <p class="text-muted">
                        Registro: {{ $estudiante->registro_universitario }}
                    </p>

                    <span class="badge {{ $estudiante->estado === 'activo' ? 'bg-success' : 'bg-secondary' }}">
                        {{ ucfirst($estudiante->estado) }}
                    </span>

                    <hr>

                    <div class="text-start">
                        <p>
                            <strong><i class="bi bi-telephone me-2"></i>Celular:</strong>
                            {{ $estudiante->celular ?? 'No registrado' }}
                        </p>

                        <p>
                            <strong><i class="bi bi-envelope me-2"></i>Correo:</strong>
                            {{ $estudiante->correo ?? 'No registrado' }}
                        </p>

                        <p>
                            <strong><i class="bi bi-book me-2"></i>Semestre:</strong>
                            {{ $estudiante->semestre ?? 'No registrado' }}
                        </p>
                    </div>

                    <a href="{{ route('estudiantes.edit', $estudiante) }}" class="btn btn-primary mt-2">
                        <i class="bi bi-pencil-square me-1"></i>
                        Editar estudiante
                    </a>

                </div>
            </div>
        </div>

        {{-- HISTORIAL DE ACTIVIDADES --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">

                <div class="card-header">
                    <h3 class="card-title">
                        <i class="bi bi-calendar-check me-2"></i>
                        Actividades inscritas
                    </h3>
                </div>

                <div class="card-body">
                    <div class="table-responsive">

                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Actividad</th>
                                    <th>Costo</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($estudiante->inscripciones as $inscripcion)
                                    <tr>
                                        <td>
                                            {{ $inscripcion->actividad?->nombre ?? 'Actividad no disponible' }}
                                        </td>

                                        <td>
                                            Bs {{ number_format($inscripcion->costo_acordado, 2) }}
                                        </td>

                                        <td>
                                            <span
                                                class="badge {{ $inscripcion->estado === 'inscrito' ? 'bg-success' : 'bg-secondary' }}">
                                                {{ ucfirst($inscripcion->estado) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">
                                            No tiene actividades registradas.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
@stop
