{{-- resources/views/movimientos_caja/index.blade.php --}}

@extends('adminlte::page')

@section('title', 'Movimientos de Caja')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/admin-brand.css') }}">
@endpush

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1 class="fw-bold">Movimientos de Caja</h1>
            <p class="text-muted mb-0">
                Control de ingresos adicionales y egresos
            </p>
        </div>

        <a href="{{ route('movimientos-caja.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>
            Nuevo movimiento
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

    @php
        $ingresos = \App\Models\MovimientoCaja::where('tipo', 'ingreso')->where('estado', 'confirmado')->sum('monto');

        $egresos = \App\Models\MovimientoCaja::where('tipo', 'egreso')->where('estado', 'confirmado')->sum('monto');
    @endphp

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card bg-success text-white border-0 shadow-sm">
                <div class="card-body">
                    <p>Otros ingresos</p>
                    <h3 class="fw-bold">
                        Bs {{ number_format($ingresos, 2) }}
                    </h3>
                    <i class="bi bi-arrow-down-circle"></i>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-danger text-white border-0 shadow-sm">
                <div class="card-body">
                    <p>Total de egresos</p>
                    <h3 class="fw-bold">
                        Bs {{ number_format($egresos, 2) }}
                    </h3>
                    <i class="bi bi-arrow-up-circle"></i>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-primary text-white border-0 shadow-sm">
                <div class="card-body">
                    <p>Balance de otros movimientos</p>
                    <h3 class="fw-bold">
                        Bs {{ number_format($ingresos - $egresos, 2) }}
                    </h3>
                    <i class="bi bi-wallet2"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header">
            <h3 class="card-title">
                <i class="bi bi-list-ul me-2"></i>
                Historial de movimientos
            </h3>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Concepto</th>
                            <th>Actividad</th>
                            <th>Monto</th>
                            <th>Responsable</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($movimientos as $movimiento)
                            <tr>
                                <td>{{ $movimiento->id }}</td>

                                <td>
                                    {{ \Carbon\Carbon::parse($movimiento->fecha_movimiento)->format('d/m/Y H:i') }}
                                </td>

                                <td>
                                    @if ($movimiento->tipo === 'ingreso')
                                        <span class="badge bg-success">
                                            <i class="bi bi-arrow-down"></i>
                                            Ingreso
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="bi bi-arrow-up"></i>
                                            Egreso
                                        </span>
                                    @endif
                                </td>

                                <td>{{ $movimiento->concepto }}</td>

                                <td>
                                    {{ $movimiento->actividad?->nombre ?? 'General' }}
                                </td>

                                <td class="fw-bold">
                                    Bs {{ number_format($movimiento->monto, 2) }}
                                </td>

                                <td>
                                    {{ $movimiento->usuario?->name ?? '—' }}
                                </td>

                                <td>
                                    @if ($movimiento->estado === 'confirmado')
                                        <span class="badge bg-success">Confirmado</span>
                                    @else
                                        <span class="badge bg-secondary">Anulado</span>
                                    @endif
                                </td>

                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('movimientos-caja.show', $movimiento) }}"
                                            class="btn btn-info btn-sm text-white" title="Ver detalle">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        @if ($movimiento->estado === 'confirmado')
                                            <form method="POST"
                                                action="{{ route('movimientos-caja.destroy', $movimiento) }}"
                                                onsubmit="return confirm('¿Anular este movimiento?')">
                                                @csrf
                                                @method('DELETE')

                                                <button class="btn btn-danger btn-sm" title="Anular">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-5">
                                    No existen movimientos registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $movimientos->links() }}
            </div>
        </div>
    </div>
@stop
