@extends('adminlte::page')

@section('title', 'Nuevo Método de Pago')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/admin-brand.css') }}?v=4{{ '' }}">
@endpush

@section('content_header')
    <h1 class="fw-bold">
        <i class="bi bi-plus-circle me-2"></i>
        Registrar método de pago
    </h1>
@stop

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-header">
            <h3 class="card-title">Datos del método de pago</h3>
        </div>

        <form method="POST" action="{{ route('metodos-pago.store') }}">
            @csrf

            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Nombre del método *</label>

                    <input type="text" name="nombre" maxlength="50"
                        class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}"
                        placeholder="Ej. Efectivo, QR, Transferencia" required>

                    @error('nombre')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <div class="card-footer d-flex justify-content-end gap-2">
                <a href="{{ route('metodos-pago.index') }}" class="btn btn-secondary">
                    Cancelar
                </a>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>
                    Guardar método
                </button>
            </div>
        </form>
    </div>
@stop
