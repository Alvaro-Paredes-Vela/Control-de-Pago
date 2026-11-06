{{-- resources/views/pagos/index.blade.php --}}

@extends('adminlte::page')

@section('title', 'Control de Pagos')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/admin-brand.css') }}?v=4">
    <style>
        .payment-card {
            border: 0;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, .12);
        }

        .payment-card .table td,
        .payment-card .table th {
            vertical-align: middle;
            white-space: nowrap;
        }

        .payment-card .table thead th {
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .05em;
            opacity: .75;
            border-top: 0;
        }

        .payment-card .table td.wrap {
            white-space: normal;
            min-width: 160px;
        }

        /* Pagos anulados: atenuados y con monto tachado */
        .row-anulado {
            opacity: .55;
        }

        .row-anulado .monto {
            text-decoration: line-through;
        }

        .monto {
            font-variant-numeric: tabular-nums;
        }

        .btn-icon {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
        }

        /* ---- Paginación (Bootstrap) ---- */
        .pagination-wrap nav {
            display: flex;
            justify-content: flex-end;
        }

        .pagination-wrap .pagination {
            margin-bottom: 0;
            flex-wrap: wrap;
        }

        /* Por si algún día vuelve la paginación Tailwind: evita SVG gigantes */
        .pagination-wrap svg {
            width: 1rem;
            height: 1rem;
        }
    </style>
@endpush

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1 class="fw-bold mb-1">Control de Pagos</h1>
            <p class="text-muted mb-0">Registro de pagos y abonos de estudiantes</p>
        </div>

        <a href="{{ route('pagos.create') }}" class="btn btn-primary">
            <i class="bi bi-cash-coin me-1"></i>
            Registrar pago
        </a>
    </div>
@stop

@section('content')

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-1"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" data-dismiss="alert"
                aria-label="Cerrar"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-1"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" data-dismiss="alert"
                aria-label="Cerrar"></button>
        </div>
    @endif

    <div class="card payment-card">
        <div class="card-header bg-transparent border-0 pt-3 pb-0">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">
                    <i class="bi bi-clock-history me-2"></i>
                    Historial de pagos
                </h5>

                <span class="badge bg-primary">
                    {{ number_format($pagos->total()) }} registros
                </span>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Fecha</th>
                            <th>Estudiante</th>
                            <th>Actividad</th>
                            <th>Método</th>
                            <th class="text-end">Monto</th>
                            <th>Recibido por</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($pagos as $pago)
                            @php
                                $estudiante = $pago->inscripcion?->estudiante;
                                $anulado = $pago->estado !== 'confirmado';
                                $fecha = \Carbon\Carbon::parse($pago->fecha_pago);
                            @endphp

                            <tr class="{{ $anulado ? 'row-anulado' : '' }}">
                                <td class="text-muted">{{ $pago->id }}</td>

                                <td>
                                    <div>{{ $fecha->format('d/m/Y') }}</div>
                                    <div class="text-muted small">{{ $fecha->format('H:i') }}</div>
                                </td>

                                <td class="wrap">
                                    <strong>
                                        {{ $estudiante?->nombres }}
                                        {{ $estudiante?->apellidos }}
                                    </strong>
                                    <div class="text-muted small">
                                        {{ $estudiante?->registro_universitario }}
                                    </div>
                                </td>

                                <td class="wrap">
                                    {{ $pago->inscripcion?->actividad?->nombre ?? '—' }}
                                </td>

                                <td>
                                    @php $metodo = $pago->metodoPago?->nombre; @endphp
                                    @if ($metodo)
                                        <span class="badge bg-secondary bg-opacity-25 text-body border">
                                            {{ $metodo }}
                                        </span>
                                    @else
                                        —
                                    @endif
                                </td>

                                <td class="text-end fw-bold monto">
                                    Bs {{ number_format($pago->monto, 2) }}
                                </td>

                                <td class="wrap">
                                    {{ $pago->receptor?->name ?? '—' }}
                                </td>

                                <td>
                                    @if ($anulado)
                                        <span class="badge bg-danger">Anulado</span>
                                    @else
                                        <span class="badge bg-success">Confirmado</span>
                                    @endif
                                </td>

                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('pagos.show', $pago) }}" class="btn btn-info btn-icon text-white"
                                            title="Ver pago">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        @unless ($anulado)
                                            <form method="POST" action="{{ route('pagos.destroy', $pago) }}"
                                                onsubmit="return confirm('¿Anular este pago? Esta acción quedará registrada.')">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="btn btn-danger btn-icon" title="Anular pago">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            </form>
                                        @endunless
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-5">
                                    <i class="bi bi-wallet2 fs-2 d-block mb-2"></i>
                                    Todavía no hay pagos registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($pagos->hasPages())
            <div class="card-footer bg-transparent border-0 pb-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pagination-wrap">
                    <div class="text-muted small">
                        Mostrando {{ $pagos->firstItem() }} a {{ $pagos->lastItem() }}
                        de {{ number_format($pagos->total()) }} pagos
                    </div>

                    {{ $pagos->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>
@stop
