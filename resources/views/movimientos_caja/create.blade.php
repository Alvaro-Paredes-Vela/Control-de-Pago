{{-- resources/views/movimientos_caja/create.blade.php --}}

@extends('adminlte::page')

@section('title', 'Nuevo Movimiento de Caja')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/admin-brand.css') }}">
@endpush

@section('content_header')
    <h1 class="fw-bold">
        <i class="bi bi-wallet2 me-2"></i>
        Registrar movimiento
    </h1>
@stop

@section('content')

    <div class="card border-0 shadow-sm">
        <div class="card-header">
            <h3 class="card-title">Datos del movimiento</h3>
        </div>

        <form action="{{ route('movimientos-caja.store') }}" method="POST">
            @csrf

            <div class="card-body">

                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Registra ingresos adicionales o gastos del Centro Interno.
                    Los pagos de estudiantes se registran en el módulo Pagos.
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Tipo de movimiento *</label>

                        <select name="tipo" class="form-select" required>
                            <option value="">Seleccione una opción</option>

                            <option value="ingreso" @selected(old('tipo') === 'ingreso')>
                                Ingreso
                            </option>

                            <option value="egreso" @selected(old('tipo') === 'egreso')>
                                Egreso
                            </option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Actividad relacionada</label>

                        <select name="actividad_id" class="form-select">
                            <option value="">Movimiento general</option>

                            @foreach ($actividades as $actividad)
                                <option value="{{ $actividad->id }}" @selected(old('actividad_id') == $actividad->id)>
                                    {{ $actividad->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Concepto *</label>

                        <input type="text" name="concepto" class="form-control" maxlength="200"
                            value="{{ old('concepto') }}" placeholder="Ej. Compra de premios para torneo" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Monto (Bs) *</label>

                        <div class="input-group">
                            <span class="input-group-text">Bs</span>

                            <input type="number" name="monto" class="form-control" min="0.01" step="0.01"
                                value="{{ old('monto') }}" placeholder="0.00" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Referencia de comprobante</label>

                        <input type="text" name="comprobante" class="form-control" value="{{ old('comprobante') }}"
                            placeholder="Ej. Factura N.º 125">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Observaciones</label>

                        <textarea name="observaciones" rows="3" class="form-control" placeholder="Información adicional">{{ old('observaciones') }}</textarea>
                    </div>

                </div>
            </div>

            <div class="card-footer d-flex justify-content-end gap-2">
                <a href="{{ route('movimientos-caja.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>
                    Cancelar
                </a>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>
                    Guardar movimiento
                </button>
            </div>
        </form>
    </div>
@stop
