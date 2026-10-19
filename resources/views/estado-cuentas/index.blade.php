{{-- resources/views/estado-cuentas/index.blade.php --}}

@extends('adminlte::page')

@section('title', 'Estado de cuentas')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/admin-brand.css') }}?v=5">

    <style>
        /* ---------- Tarjetas base ---------- */
        .account-card {
            border: 0;
            border-radius: 14px;
            box-shadow: 0 4px 18px rgba(15, 23, 42, .07);
        }

        /* ---------- Resumen ---------- */
        .stat-card {
            border: 0;
            border-radius: 14px;
            box-shadow: 0 4px 18px rgba(15, 23, 42, .07);
            height: 100%;
            overflow: hidden;
            position: relative;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            inset: 0 auto 0 0;
            width: 4px;
            background: var(--stat-color, #0d6efd);
        }

        .stat-card .stat-body {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px 18px;
        }

        .stat-icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            font-size: 1.3rem;
            color: var(--stat-color, #0d6efd);
            background: color-mix(in srgb, var(--stat-color, #0d6efd) 12%, transparent);
            flex-shrink: 0;
        }

        .stat-label {
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #6c757d;
            margin: 0;
        }

        .stat-value {
            font-size: 1.3rem;
            font-weight: 700;
            line-height: 1.2;
            margin: 0;
            white-space: nowrap;
        }

        /* ---------- Filtros ---------- */
        .account-search {
            background: color-mix(in srgb, var(--bs-body-color, #212529) 3%, var(--bs-body-bg, #fff));
            border: 1px solid rgba(0, 0, 0, .06);
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 22px;
        }

        .account-search label {
            font-size: .78rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #6c757d;
            margin-bottom: 6px;
        }

        /* ---------- Tabla ---------- */
        .account-table {
            margin-bottom: 0;
        }

        .account-table thead th {
            font-size: .74rem;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #6c757d;
            font-weight: 700;
            border-bottom: 2px solid rgba(0, 0, 0, .08);
            white-space: nowrap;
        }

        .account-table td {
            vertical-align: middle;
            padding-top: .85rem;
            padding-bottom: .85rem;
        }

        .account-table tbody tr {
            transition: background-color .15s ease;
        }

        .amount {
            white-space: nowrap;
            font-weight: 600;
            font-variant-numeric: tabular-nums;
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            font-size: .85rem;
            font-weight: 700;
            color: #0d6efd;
            background: rgba(13, 110, 253, .1);
            flex-shrink: 0;
        }

        .activity-chip {
            display: inline-block;
            padding: .25rem .6rem;
            border-radius: 8px;
            font-size: .82rem;
            background: rgba(108, 117, 125, .1);
        }

        /* ---------- Estado ---------- */
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: .25rem .65rem;
            border-radius: 999px;
            font-size: .76rem;
            font-weight: 600;
        }

        .status-pill::before {
            content: '';
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: currentColor;
        }

        .status-success {
            color: #146c43;
            background: rgba(25, 135, 84, .12);
        }

        .status-warning {
            color: #946200;
            background: rgba(255, 193, 7, .2);
        }

        .status-danger {
            color: #b02a37;
            background: rgba(220, 53, 69, .12);
        }

        .pay-progress {
            height: 6px;
            min-width: 110px;
            border-radius: 999px;
            background: rgba(0, 0, 0, .07);
        }

        .pay-progress .progress-bar {
            border-radius: 999px;
        }

        .empty-state {
            padding: 3.5rem 0;
        }

        .empty-state i {
            font-size: 2.6rem;
            opacity: .45;
        }
    </style>
@endpush

@section('content_header')
    <div>
        <h1 class="fw-bold mb-1">Estado de cuentas</h1>

        <p class="text-muted mb-0">
            Consulta cuánto pagó y cuánto debe cada estudiante por actividad.
        </p>
    </div>
@stop

@section('content')

    @php
        // Totales para las tarjetas de resumen.
        // Si el controlador envía $resumen (totales globales) se usa ese; de lo contrario se calcula con la página actual.
        $pagina = $inscripciones->getCollection();

        $totalCosto = $resumen['costo'] ?? $pagina->sum(fn($i) => (float) $i->costo_acordado);
        $totalPagado = $resumen['pagado'] ?? $pagina->sum(fn($i) => (float) ($i->total_pagado ?? 0));
        $totalDebe = $resumen['debe'] ?? max(0, $totalCosto - $totalPagado);
        $alcance = isset($resumen) ? 'Total general' : 'En esta página';
    @endphp

    {{-- Mensajes del sistema --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Resumen --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card stat-card" style="--stat-color:#0d6efd">
                <div class="stat-body">
                    <div class="stat-icon"><i class="bi bi-people"></i></div>
                    <div>
                        <p class="stat-label">Cuentas</p>
                        <p class="stat-value">{{ number_format($inscripciones->total(), 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card stat-card" style="--stat-color:#6f42c1">
                <div class="stat-body">
                    <div class="stat-icon"><i class="bi bi-receipt"></i></div>
                    <div>
                        <p class="stat-label">Costo · {{ $alcance }}</p>
                        <p class="stat-value">Bs {{ number_format($totalCosto, 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card stat-card" style="--stat-color:#198754">
                <div class="stat-body">
                    <div class="stat-icon"><i class="bi bi-cash-coin"></i></div>
                    <div>
                        <p class="stat-label">Pagado · {{ $alcance }}</p>
                        <p class="stat-value text-success">Bs {{ number_format($totalPagado, 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card stat-card" style="--stat-color:#dc3545">
                <div class="stat-body">
                    <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
                    <div>
                        <p class="stat-label">Por cobrar · {{ $alcance }}</p>
                        <p class="stat-value text-danger">Bs {{ number_format($totalDebe, 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card account-card">
        <div class="card-body">

            {{-- Buscador y filtro por actividad --}}
            <form action="{{ route('estado-cuentas.index') }}" method="GET" class="account-search">
                <div class="row align-items-end g-3">

                    <div class="col-md-5">
                        <label for="buscar">Buscar estudiante</label>

                        <div class="input-group">
                            <span class="input-group-text bg-transparent"><i class="bi bi-search"></i></span>
                            <input type="text" name="buscar" id="buscar" class="form-control"
                                placeholder="Registro, nombres o apellidos..." value="{{ $buscar ?? request('buscar') }}">
                        </div>
                    </div>

                    <div class="col-md-5">
                        <label for="actividad_id">Actividad</label>

                        <select name="actividad_id" id="actividad_id" class="form-control">
                            <option value="">Todas las actividades</option>

                            @foreach ($actividades as $actividad)
                                <option value="{{ $actividad->id }}" @selected((string) ($actividadId ?? request('actividad_id')) === (string) $actividad->id)>
                                    {{ $actividad->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1" title="Buscar">
                                <i class="bi bi-funnel me-1"></i> Filtrar
                            </button>

                            <a href="{{ route('estado-cuentas.index') }}" class="btn btn-outline-secondary"
                                title="Limpiar filtros">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>
                        </div>
                    </div>

                </div>
            </form>

            {{-- Encabezado de la tabla --}}
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                <h5 class="fw-bold mb-2">
                    <i class="bi bi-file-earmark-spreadsheet me-2 text-primary"></i>
                    Cuentas de estudiantes
                </h5>

                <span class="badge rounded-pill bg-primary-subtle text-primary-emphasis px-3 py-2">
                    {{ $inscripciones->total() }} cuentas
                </span>
            </div>

            {{-- Tabla de cuentas --}}
            <div class="table-responsive">
                <table class="table table-hover account-table">

                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>Actividad</th>
                            <th class="text-end">Costo</th>
                            <th class="text-end">Pagado</th>
                            <th class="text-end">Debe</th>
                            <th style="min-width: 150px;">Estado</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($inscripciones as $inscripcion)
                            @php
                                // Costo acordado para esta inscripción.
                                $costo = (float) $inscripcion->costo_acordado;

                                // Suma de los pagos confirmados.
                                $pagado = (float) ($inscripcion->total_pagado ?? 0);

                                // Saldo pendiente.
                                $debe = max(0, $costo - $pagado);

                                // Estado de la cuenta.
                                if ($debe <= 0) {
                                    $estado = 'Completo';
                                    $color = 'success';
                                } elseif ($pagado > 0) {
                                    $estado = 'Parcial';
                                    $color = 'warning';
                                } else {
                                    $estado = 'Pendiente';
                                    $color = 'danger';
                                }

                                // Porcentaje pagado para la barra.
                                $porcentaje = $costo > 0 ? min(100, ($pagado / $costo) * 100) : 100;

                                // Iniciales para el avatar.
                                $iniciales = mb_strtoupper(
                                    mb_substr($inscripcion->estudiante?->nombres ?? '', 0, 1) .
                                        mb_substr($inscripcion->estudiante?->apellidos ?? '', 0, 1),
                                );
                            @endphp

                            <tr>
                                {{-- Información del estudiante --}}
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar">{{ $iniciales ?: '?' }}</div>

                                        <div>
                                            <strong>
                                                {{ $inscripcion->estudiante?->apellidos ?? '' }}
                                                {{ $inscripcion->estudiante?->nombres ?? '' }}
                                            </strong>

                                            <div class="text-muted small">
                                                <i class="bi bi-person-vcard me-1"></i>
                                                {{ $inscripcion->estudiante?->registro_universitario ?? '—' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Actividad a la que se inscribió --}}
                                <td>
                                    <span class="activity-chip">
                                        {{ $inscripcion->actividad?->nombre ?? 'Actividad no disponible' }}
                                    </span>
                                </td>

                                {{-- Costo total --}}
                                <td class="text-end amount">
                                    Bs {{ number_format($costo, 2, ',', '.') }}
                                </td>

                                {{-- Dinero recibido y confirmado --}}
                                <td class="text-end amount text-success">
                                    Bs {{ number_format($pagado, 2, ',', '.') }}
                                </td>

                                {{-- Saldo pendiente --}}
                                <td class="text-end amount {{ $debe > 0 ? 'text-danger' : 'text-muted' }}">
                                    Bs {{ number_format($debe, 2, ',', '.') }}
                                </td>

                                {{-- Estado y porcentaje pagado --}}
                                <td>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="status-pill status-{{ $color }}">{{ $estado }}</span>
                                        <small class="text-muted fw-semibold">
                                            {{ number_format($porcentaje, 0) }}%
                                        </small>
                                    </div>

                                    <div class="progress pay-progress"
                                        title="{{ number_format($porcentaje, 1) }}% pagado">
                                        <div class="progress-bar bg-{{ $color }}" role="progressbar"
                                            style="width: {{ $porcentaje }}%" aria-valuenow="{{ $porcentaje }}"
                                            aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted empty-state">
                                    <i class="bi bi-clipboard2-x d-block mb-2"></i>
                                    <div class="fw-semibold">No se encontraron cuentas</div>
                                    <small>Prueba con otro término de búsqueda o cambia la actividad.</small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

            {{-- Paginación que conserva los filtros --}}
            <div class="d-flex justify-content-between align-items-center flex-wrap mt-4">
                <small class="text-muted mb-2">
                    Mostrando {{ $inscripciones->firstItem() ?? 0 }}–{{ $inscripciones->lastItem() ?? 0 }}
                    de {{ $inscripciones->total() }}
                </small>

                {{ $inscripciones->withQueryString()->links() }}
            </div>

        </div>
    </div>

@stop
