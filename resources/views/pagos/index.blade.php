{{-- resources/views/pagos/index.blade.php --}}

@extends('adminlte::page')

@section('title', 'Control de Pagos')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/admin-brand.css') }}?v=4{{ '' }}">
    <style>
        .payment-card {
            border: 0;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, .07);
        }

        .table td,
        .table th {
            vertical-align: middle;
        }
    </style>
@endpush

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1 class="fw-bold">Control de Pagos</h1>
            <p class="text-muted mb-0">
                Registro de pagos y abonos de estudiantes
            </p>
        </div>

        <a href="{{ route('pagos.create') }}" class="btn btn-primary">
            <i class="bi bi-cash-coin me-1"></i>
            Registrar pago
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

    <div class="card payment-card">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">
                    <i class="bi bi-clock-history me-2"></i>
                    Historial de pagos
                </h5>

                <span class="badge bg-primary">
                    {{ $pagos->total() }} registros
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Fecha</th>
                            <th>Estudiante</th>
                            <th>Actividad</th>
                            <th>Método</th>
                            <th>Monto</th>
                            <th>Recibido por</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($pagos as $pago)
                            <tr>
                                <td>{{ $pago->id }}</td>

                                <td>
                                    {{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y H:i') }}
                                </td>

                                <td>
                                    <strong>
                                        {{ $pago->inscripcion?->estudiante?->nombres }}
                                        {{ $pago->inscripcion?->estudiante?->apellidos }}
                                    </strong>
                                    <div class="text-muted small">
                                        {{ $pago->inscripcion?->estudiante?->registro_universitario }}
                                    </div>
                                </td>

                                <td>
                                    {{ $pago->inscripcion?->actividad?->nombre ?? '—' }}
                                </td>

                                <td>
                                    {{ $pago->metodoPago?->nombre ?? '—' }}
                                </td>

                                <td class="fw-bold">
                                    Bs {{ number_format($pago->monto, 2) }}
                                </td>

                                <td>
                                    {{ $pago->receptor?->name ?? '—' }}
                                </td>

                                <td>
                                    @if ($pago->estado === 'confirmado')
                                        <span class="badge bg-success">Confirmado</span>
                                    @else
                                        <span class="badge bg-danger">Anulado</span>
                                    @endif
                                </td>

                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('pagos.show', $pago) }}" class="btn btn-info btn-sm text-white"
                                            title="Ver pago">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        @if ($pago->estado === 'confirmado')
                                            <form method="POST" action="{{ route('pagos.destroy', $pago) }}"
                                                onsubmit="return confirm('¿Anular este pago? Esta acción quedará registrada.')">
                                                @csrf
                                                @method('DELETE')

                                                <button class="btn btn-danger btn-sm" title="Anular pago">
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
                                    <i class="bi bi-wallet2 fs-2 d-block mb-2"></i>
                                    Todavía no hay pagos registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $pagos->links() }}
            </div>
        </div>
    </div>
@stop
