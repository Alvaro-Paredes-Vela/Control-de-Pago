{{-- resources/views/reportes/estudiantes-actividad.blade.php --}}

@extends('adminlte::page')

@section('title', 'Reporte de estudiantes por actividad')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/admin-brand.css') }}?v=5">

    <style>
        .report-card {
            border: 0;
            border-radius: 14px;
            box-shadow: 0 4px 18px rgba(15, 23, 42, .07);
        }

        .report-filter {
            background: color-mix(in srgb, var(--bs-body-color, #212529) 3%, var(--bs-body-bg, #fff));
            border: 1px solid rgba(0, 0, 0, .06);
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 22px;
        }

        .report-filter label {
            font-size: .78rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #6c757d;
            margin-bottom: 6px;
        }

        .report-table thead th {
            font-size: .74rem;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #6c757d;
            font-weight: 700;
            border-bottom: 2px solid rgba(0, 0, 0, .08);
            white-space: nowrap;
        }

        .report-table td {
            vertical-align: middle;
            padding-top: .75rem;
            padding-bottom: .75rem;
        }

        .avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            font-size: .82rem;
            font-weight: 700;
            color: #0d6efd;
            background: rgba(13, 110, 253, .1);
            flex-shrink: 0;
        }

        .reg-chip {
            display: inline-block;
            padding: .2rem .6rem;
            border-radius: 8px;
            font-size: .85rem;
            font-variant-numeric: tabular-nums;
            background: rgba(108, 117, 125, .12);
        }

        .empty-state {
            padding: 3.5rem 0;
        }

        .empty-state i {
            font-size: 2.6rem;
            opacity: .45;
        }

        /* Paginación */
        .pagination-wrap nav {
            display: flex;
            justify-content: flex-end;
        }

        .pagination-wrap .pagination {
            margin-bottom: 0;
            flex-wrap: wrap;
        }

        .pagination-wrap nav p.small {
            display: none;
        }

        .pagination-wrap svg {
            width: 1rem;
            height: 1rem;
        }
    </style>
@endpush

@section('content_header')
    <div>
        <h1 class="fw-bold mb-1">Reporte de estudiantes por actividad</h1>
        <p class="text-muted mb-0">
            Lista de estudiantes inscritos en una actividad, lista para exportar a Excel.
        </p>
    </div>
@stop

@section('content')

    <div class="card report-card">
        <div class="card-body">

            {{-- Selector de actividad --}}
            <form action="{{ route('reportes.estudiantes-actividad') }}" method="GET" class="report-filter">
                <div class="row align-items-end g-3">
                    <div class="col-md-8">
                        <label for="actividad_id">Actividad</label>

                        <select name="actividad_id" id="actividad_id" class="form-control" required>
                            <option value="">Selecciona una actividad...</option>

                            @foreach ($actividades as $item)
                                <option value="{{ $item->id }}" @selected((string) request('actividad_id') === (string) $item->id)>
                                    {{ $item->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="bi bi-search me-1"></i> Generar reporte
                            </button>

                            @if ($actividad)
                                <a href="{{ route('reportes.estudiantes-actividad') }}" class="btn btn-outline-secondary"
                                    title="Limpiar">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </form>

            @if (!$actividad)
                {{-- Estado inicial: aún no se eligió actividad --}}
                <div class="text-center text-muted empty-state">
                    <i class="bi bi-file-earmark-spreadsheet d-block mb-2"></i>
                    <div class="fw-semibold">Elige una actividad para ver el reporte</div>
                    <small>Se mostrarán los estudiantes con inscripción activa.</small>
                </div>
            @else
                {{-- Encabezado del reporte --}}
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <div>
                        <h5 class="fw-bold mb-1">
                            <i class="bi bi-people me-2 text-primary"></i>
                            {{ $actividad->nombre }}
                        </h5>

                        <span class="badge rounded-pill bg-primary-subtle text-primary-emphasis px-3 py-2">
                            {{ number_format($estudiantes->total(), 0, ',', '.') }}
                            {{ $estudiantes->total() === 1 ? 'estudiante' : 'estudiantes' }}
                        </span>
                    </div>

                    @if ($estudiantes->total() > 0)
                        <a href="{{ route('reportes.estudiantes-actividad.exportar', ['actividad_id' => $actividad->id]) }}"
                            class="btn btn-success">
                            <i class="bi bi-file-earmark-excel me-1"></i>
                            Exportar a Excel
                        </a>
                    @endif
                </div>

                {{-- Tabla --}}
                <div class="table-responsive">
                    <table class="table table-hover report-table mb-0">
                        <thead>
                            <tr>
                                <th style="width: 70px;">N°</th>
                                <th>Nombre completo</th>
                                <th>Registro universitario</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($estudiantes as $estudiante)
                                @php
                                    $iniciales = mb_strtoupper(
                                        mb_substr($estudiante->nombres ?? '', 0, 1) .
                                            mb_substr($estudiante->apellidos ?? '', 0, 1),
                                    );
                                @endphp

                                <tr>
                                    <td class="text-muted">{{ $estudiantes->firstItem() + $loop->index }}</td>

                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar">{{ $iniciales ?: '?' }}</div>
                                            <strong>{{ $estudiante->apellidos }} {{ $estudiante->nombres }}</strong>
                                        </div>
                                    </td>

                                    <td>
                                        <span class="reg-chip">{{ $estudiante->registro_universitario }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted empty-state">
                                        <i class="bi bi-person-x d-block mb-2"></i>
                                        <div class="fw-semibold">Esta actividad no tiene estudiantes inscritos</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Paginación --}}
                @if ($estudiantes->total() > 0)
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-4 pagination-wrap">
                        <small class="text-muted">
                            Mostrando {{ $estudiantes->firstItem() }}–{{ $estudiantes->lastItem() }}
                            de {{ number_format($estudiantes->total(), 0, ',', '.') }}
                        </small>

                        @if ($estudiantes->hasPages())
                            {{ $estudiantes->onEachSide(1)->links('pagination::bootstrap-5') }}
                        @endif
                    </div>
                @endif
            @endif

        </div>
    </div>
@stop
