{{-- resources/views/inscripciones/show.blade.php --}}

@extends('adminlte::page')

@section('title', 'Detalle de inscripción')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/admin-brand.css') }}">

    <style>
        .detail-card {
            border: 0;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, .06);
        }

        .amount-box {
            padding: 20px;
            border-radius: 10px;
            background: rgba(100, 116, 139, .08);
        }

        .amount-box h3 {
            font-weight: 700;
            margin: 8px 0 0;
        }
    </style>
@endpush

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1 class="fw-bold">Detalle de inscripción</h1>
            <p class="text-muted mb-0">
                Inscripción #{{ $inscripcion->id }}
            </p>
        </div>

        <a href="{{ route('inscripciones.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Volver
        </a>
    </div>
@stop

@section('content')

    {{-- CALCULAR PAGOS SOLO PARA ROLES AUTORIZADOS --}}
    @if ($esFinanciero)
        @php
            $pagosConfirmados = $inscripcion->pagos->where('estado', 'confirmado');

            $totalPagado = $pagosConfirmados->sum('monto');

            $saldoPendiente = max(0, $inscripcion->costo_acordado - $totalPagado);

            $porcentaje =
                $inscripcion->costo_acordado > 0 ? min(100, ($totalPagado / $inscripcion->costo_acordado) * 100) : 100;
        @endphp
    @endif

    <div class="row g-3 mb-4">

        {{-- INFORMACIÓN DE LA INSCRIPCIÓN --}}
        <div class="{{ $esFinanciero ? 'col-lg-6' : 'col-lg-12' }}">

            <div class="card detail-card h-100">

                <div class="card-header">
                    <h3 class="card-title">
                        <i class="bi bi-person-vcard me-2"></i>
                        Información de inscripción
                    </h3>
                </div>

                <div class="card-body">

                    <h5 class="fw-bold">
                        {{ $inscripcion->estudiante?->nombres }}
                        {{ $inscripcion->estudiante?->apellidos }}
                    </h5>

                    <p class="text-muted">
                        Registro:
                        {{ $inscripcion->estudiante?->registro_universitario }}
                    </p>

                    <hr>

                    <p>
                        <strong>Actividad:</strong>
                        {{ $inscripcion->actividad?->nombre }}
                    </p>

                    <p>
                        <strong>Fecha de inscripción:</strong>
                        {{ $inscripcion->created_at?->format('d/m/Y H:i') }}
                    </p>

                    <p>
                        <strong>Estado:</strong>

                        @if ($inscripcion->estado === 'inscrito')
                            <span class="badge bg-success">
                                Inscrito
                            </span>
                        @else
                            <span class="badge bg-secondary">
                                Cancelado
                            </span>
                        @endif
                    </p>

                    <p>
                        <strong>Observaciones:</strong>
                        {{ $inscripcion->observaciones ?: 'Sin observaciones' }}
                    </p>

                    {{-- CAMPOS PERSONALIZADOS --}}
                    @if ($inscripcion->respuestas->isNotEmpty())

                        <hr>

                        <h5 class="fw-bold mb-3">
                            <i class="bi bi-ui-checks me-2"></i>
                            Datos adicionales
                        </h5>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody>

                                    @foreach ($inscripcion->respuestas as $respuesta)
                                        <tr>
                                            <th style="width: 40%;">
                                                {{ $respuesta->campo?->etiqueta ?? 'Campo' }}
                                            </th>

                                            <td>
                                                @if ($respuesta->campo?->tipo === 'booleano')
                                                    {{ $respuesta->valor === '1' ? 'Sí' : 'No' }}
                                                @else
                                                    {{ $respuesta->valor }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>

                    @endif

                    <a href="{{ route('inscripciones.edit', $inscripcion) }}" class="btn btn-warning mt-2">
                        <i class="bi bi-pencil-square me-1"></i>
                        Editar inscripción
                    </a>

                </div>
            </div>
        </div>

        {{-- RESUMEN FINANCIERO: SOLO ADMINISTRADOR Y TESORERO --}}
        @if ($esFinanciero)

            <div class="col-lg-6">

                <div class="card detail-card h-100">

                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="bi bi-wallet2 me-2"></i>
                            Resumen de pagos
                        </h3>
                    </div>

                    <div class="card-body">

                        <div class="row g-3 mb-4">

                            <div class="col-6">
                                <div class="amount-box">
                                    <small class="text-muted">
                                        Costo total
                                    </small>

                                    <h3>
                                        Bs {{ number_format($inscripcion->costo_acordado, 2) }}
                                    </h3>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="amount-box">
                                    <small class="text-muted">
                                        Total pagado
                                    </small>

                                    <h3 class="text-success">
                                        Bs {{ number_format($totalPagado, 2) }}
                                    </h3>
                                </div>
                            </div>

                        </div>

                        {{-- PROGRESO DEL PAGO --}}
                        <div class="d-flex justify-content-between mb-2">
                            <strong>Progreso del pago</strong>
                            <span>
                                {{ number_format($porcentaje, 0) }}%
                            </span>
                        </div>

                        <div class="progress mb-4" style="height: 12px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $porcentaje }}%;"
                                aria-valuenow="{{ $porcentaje }}" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <strong>Saldo pendiente</strong>

                            <h4 class="fw-bold {{ $saldoPendiente > 0 ? 'text-danger' : 'text-success' }}">
                                Bs {{ number_format($saldoPendiente, 2) }}
                            </h4>
                        </div>

                        {{-- ESTADO DE PAGO --}}
                        @if ($inscripcion->costo_acordado == 0)
                            <div class="alert alert-info mt-3 mb-0">
                                Actividad gratuita.
                            </div>
                        @elseif ($saldoPendiente <= 0)
                            <div class="alert alert-success mt-3 mb-0">
                                <i class="bi bi-check-circle me-1"></i>
                                Pago completado.
                            </div>
                        @elseif ($inscripcion->estado === 'inscrito')
                            <a href="{{ route('pagos.create', ['inscripcion_id' => $inscripcion->id]) }}"
                                class="btn btn-success mt-3">
                                <i class="bi bi-cash-coin me-1"></i>
                                Registrar abono
                            </a>
                        @endif

                    </div>
                </div>
            </div>

        @endif

    </div>

    {{-- HISTORIAL: SOLO ADMINISTRADOR Y TESORERO --}}
    @if ($esFinanciero)

        <div class="card detail-card mb-4">

            <div class="card-header">
                <h3 class="card-title">
                    <i class="bi bi-clock-history me-2"></i>
                    Historial de pagos
                </h3>
            </div>

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-hover table-striped">

                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Fecha</th>
                                <th>Monto</th>
                                <th>Estado</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($inscripcion->pagos as $pago)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td>
                                        {{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y H:i') }}
                                    </td>

                                    <td>
                                        Bs {{ number_format($pago->monto, 2) }}
                                    </td>

                                    <td>
                                        @if ($pago->estado === 'confirmado')
                                            <span class="badge bg-success">
                                                Confirmado
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                Anulado
                                            </span>
                                        @endif
                                    </td>
                                </tr>

                            @empty

                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        Todavía no se registraron pagos.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>

            </div>
        </div>

    @endif

@stop
