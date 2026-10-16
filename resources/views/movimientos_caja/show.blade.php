{{-- resources/views/movimientos_caja/show.blade.php --}}

@extends('adminlte::page')

@section('title', 'Detalle de Movimiento')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/admin-brand.css') }}?v=4{{ '' }}">
@endpush

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h1 class="fw-bold">Detalle del movimiento</h1>

        <a href="{{ route('movimientos-caja.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Volver
        </a>
    </div>
@stop

@section('content')

    <div class="card border-0 shadow-sm">
        <div class="card-header">
            <h3 class="card-title">
                Movimiento #{{ $movimiento->id }}
            </h3>
        </div>

        <div class="card-body">

            <div class="text-center mb-4">
                @if ($movimiento->tipo === 'ingreso')
                    <i class="bi bi-arrow-down-circle text-success" style="font-size: 4rem;"></i>
                @else
                    <i class="bi bi-arrow-up-circle text-danger" style="font-size: 4rem;"></i>
                @endif

                <h2 class="fw-bold mt-3">
                    Bs {{ number_format($movimiento->monto, 2) }}
                </h2>

                <span class="badge {{ $movimiento->tipo === 'ingreso' ? 'bg-success' : 'bg-danger' }}">
                    {{ ucfirst($movimiento->tipo) }}
                </span>

                <span class="badge {{ $movimiento->estado === 'confirmado' ? 'bg-primary' : 'bg-secondary' }}">
                    {{ ucfirst($movimiento->estado) }}
                </span>
            </div>

            <hr>

            <div class="row g-3">
                <div class="col-md-6">
                    <strong>Concepto:</strong>
                    <p>{{ $movimiento->concepto }}</p>
                </div>

                <div class="col-md-6">
                    <strong>Actividad relacionada:</strong>
                    <p>
                        {{ $movimiento->actividad?->nombre ?? 'Movimiento general' }}
                    </p>
                </div>

                <div class="col-md-6">
                    <strong>Fecha:</strong>
                    <p>
                        {{ \Carbon\Carbon::parse($movimiento->fecha_movimiento)->format('d/m/Y H:i') }}
                    </p>
                </div>

                <div class="col-md-6">
                    <strong>Registrado por:</strong>
                    <p>{{ $movimiento->usuario?->name ?? '—' }}</p>
                </div>

                <div class="col-md-6">
                    <strong>Referencia de comprobante:</strong>
                    <p>
                        {{ $movimiento->comprobante ?? 'No registrada' }}
                    </p>
                </div>

                <div class="col-12">
                    <strong>Observaciones:</strong>
                    <p>
                        {{ $movimiento->observaciones ?? 'Sin observaciones' }}
                    </p>
                </div>
            </div>

            <div class="d-flex gap-2 mt-3">
                <a href="{{ route('movimientos-caja.index') }}" class="btn btn-secondary">
                    Regresar
                </a>

                @if ($movimiento->estado === 'confirmado')
                    <form method="POST" action="{{ route('movimientos-caja.destroy', $movimiento) }}"
                        onsubmit="return confirm('¿Anular este movimiento?')">
                        @csrf
                        @method('DELETE')

                        <button class="btn btn-danger">
                            <i class="bi bi-x-circle me-1"></i>
                            Anular movimiento
                        </button>
                    </form>
                @endif
            </div>

        </div>
    </div>
@stop
