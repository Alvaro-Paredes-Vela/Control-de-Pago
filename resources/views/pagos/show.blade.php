{{-- resources/views/pagos/show.blade.php --}}

@extends('adminlte::page')

@section('title', 'Detalle de Pago')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/admin-brand.css') }}?v=4{{ '' }}">
@endpush

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h1 class="fw-bold">Detalle del pago</h1>

        <a href="{{ route('pagos.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Volver
        </a>
    </div>
@stop

@section('content')

    <div class="card border-0 shadow-sm">
        <div class="card-header">
            <h3 class="card-title">
                Pago #{{ $pago->id }}
            </h3>
        </div>

        <div class="card-body">

            <div class="text-center mb-4">
                <i class="bi bi-receipt text-primary" style="font-size: 4rem;"></i>

                <h2 class="fw-bold mt-2">
                    Bs {{ number_format($pago->monto, 2) }}
                </h2>

                <span class="badge {{ $pago->estado === 'confirmado' ? 'bg-success' : 'bg-danger' }}">
                    {{ ucfirst($pago->estado) }}
                </span>
            </div>

            <hr>

            <div class="row g-3">
                <div class="col-md-6">
                    <strong>Estudiante:</strong>
                    <p>
                        {{ $pago->inscripcion?->estudiante?->nombres }}
                        {{ $pago->inscripcion?->estudiante?->apellidos }}
                    </p>
                </div>

                <div class="col-md-6">
                    <strong>Registro universitario:</strong>
                    <p>
                        {{ $pago->inscripcion?->estudiante?->registro_universitario }}
                    </p>
                </div>

                <div class="col-md-6">
                    <strong>Actividad:</strong>
                    <p>{{ $pago->inscripcion?->actividad?->nombre }}</p>
                </div>

                <div class="col-md-6">
                    <strong>Fecha del pago:</strong>
                    <p>
                        {{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y H:i') }}
                    </p>
                </div>

                <div class="col-md-6">
                    <strong>Método de pago:</strong>
                    <p>{{ $pago->metodoPago?->nombre ?? '—' }}</p>
                </div>

                <div class="col-md-6">
                    <strong>Recibido por:</strong>
                    <p>{{ $pago->receptor?->name ?? '—' }}</p>
                </div>

                <div class="col-md-6">
                    <strong>Referencia:</strong>
                    <p>{{ $pago->referencia ?? 'Sin referencia' }}</p>
                </div>

                <div class="col-12">
                    <strong>Observaciones:</strong>
                    <p>{{ $pago->observaciones ?? 'Sin observaciones' }}</p>
                </div>
            </div>

            <div class="d-flex gap-2 mt-3">
                <a href="{{ route('pagos.index') }}" class="btn btn-secondary">
                    Regresar
                </a>

                @if ($pago->estado === 'confirmado')
                    <form action="{{ route('pagos.destroy', $pago) }}" method="POST"
                        onsubmit="return confirm('¿Seguro que deseas anular este pago?')">
                        @csrf
                        @method('DELETE')

                        <button class="btn btn-danger">
                            <i class="bi bi-x-circle me-1"></i>
                            Anular pago
                        </button>
                    </form>
                @endif
            </div>

        </div>
    </div>
@stop
