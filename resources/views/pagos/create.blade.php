{{-- resources/views/pagos/create.blade.php --}}

@extends('adminlte::page')

@section('title', 'Registrar pago')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/admin-brand.css') }}?v=4{{ '' }}">

    <style>
        .qr-card {
            border: 1px solid rgba(100, 116, 139, .2);
            border-radius: 12px;
            background: rgba(100, 116, 139, .04);
        }

        .qr-image {
            max-width: 280px;
            width: 100%;
            height: auto;
            border-radius: 10px;
        }

        .payment-summary {
            padding: 16px;
            border-radius: 10px;
            background: rgba(100, 116, 139, .08);
        }
    </style>
@endpush

@section('content_header')
    <h1 class="fw-bold">
        <i class="bi bi-cash-coin me-2"></i>
        Registrar pago
    </h1>
@stop

@section('content')

    <div class="card border-0 shadow-sm">

        <div class="card-header">
            <h3 class="card-title">Datos del pago</h3>
        </div>

        <form action="{{ route('pagos.store') }}" method="POST">
            @csrf

            <div class="card-body">

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Busca al estudiante por registro universitario o apellido.
                    Selecciona su inscripción y registra el monto abonado.
                </div>

                <div class="row g-3">

                    {{-- BUSCADOR --}}
                    <div class="col-12">

                        <label class="form-label">
                            Buscar estudiante
                        </label>

                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-search"></i>
                            </span>

                            <input type="text" id="buscar_inscripcion" class="form-control"
                                placeholder="Escribe registro, apellido o nombre..." autocomplete="off">
                        </div>

                    </div>

                    {{-- INSCRIPCIÓN --}}
                    <div class="col-12">

                        <label class="form-label">
                            Estudiante e inscripción *
                        </label>

                        <select name="inscripcion_id" id="inscripcion_id" class="form-select" required>

                            <option value="">
                                Seleccione una inscripción
                            </option>

                            @foreach ($inscripciones as $inscripcion)
                                @php
                                    $pagado = $inscripcion->pagos->where('estado', 'confirmado')->sum('monto');

                                    $saldo = max(0, $inscripcion->costo_acordado - $pagado);
                                @endphp

                                <option value="{{ $inscripcion->id }}" data-costo="{{ $inscripcion->costo_acordado }}"
                                    data-pagado="{{ $pagado }}" data-saldo="{{ $saldo }}"
                                    @selected(old('inscripcion_id', request('inscripcion_id')) == $inscripcion->id)>

                                    {{ $inscripcion->estudiante?->registro_universitario }}
                                    - {{ $inscripcion->estudiante?->apellidos }}
                                    {{ $inscripcion->estudiante?->nombres }}
                                    | {{ $inscripcion->actividad?->nombre }}
                                    | Pendiente: Bs {{ number_format($saldo, 2) }}

                                </option>
                            @endforeach
                        </select>

                        <small class="text-muted">
                            Selecciona la actividad donde se registrará el pago.
                        </small>

                        <div id="mensaje_inscripciones" class="text-danger small mt-1" style="display:none;">
                            No se encontraron inscripciones.
                        </div>

                    </div>

                    {{-- RESUMEN FINANCIERO --}}
                    <div class="col-12">

                        <div class="payment-summary">

                            <div class="row g-3 text-center">

                                <div class="col-md-4">
                                    <small class="text-muted">
                                        Costo total
                                    </small>

                                    <h5 class="fw-bold mb-0" id="resumen_costo">
                                        Bs 0.00
                                    </h5>
                                </div>

                                <div class="col-md-4">
                                    <small class="text-muted">
                                        Total pagado
                                    </small>

                                    <h5 class="fw-bold text-success mb-0" id="resumen_pagado">
                                        Bs 0.00
                                    </h5>
                                </div>

                                <div class="col-md-4">
                                    <small class="text-muted">
                                        Saldo pendiente
                                    </small>

                                    <h5 class="fw-bold text-danger mb-0" id="resumen_saldo">
                                        Bs 0.00
                                    </h5>
                                </div>

                            </div>
                        </div>

                    </div>

                    {{-- MÉTODO DE PAGO --}}
                    <div class="col-md-6">

                        <label class="form-label">
                            Método de pago *
                        </label>

                        <select name="metodo_pago_id" id="metodo_pago_id" class="form-select" required>

                            <option value="">
                                Seleccione un método
                            </option>

                            @foreach ($metodos as $metodo)
                                <option value="{{ $metodo->id }}" data-nombre="{{ $metodo->nombre }}"
                                    @selected(old('metodo_pago_id') == $metodo->id)>

                                    {{ $metodo->nombre }}

                                </option>
                            @endforeach
                        </select>

                    </div>

                    {{-- MONTO DEL ABONO --}}
                    <div class="col-md-6">

                        <label class="form-label">
                            Monto del abono (Bs) *
                        </label>

                        <div class="input-group">
                            <span class="input-group-text">Bs</span>

                            <input type="number" name="monto" id="monto" class="form-control" min="0.01"
                                step="0.01" value="{{ old('monto') }}" placeholder="Ej. 70.00" required>
                        </div>

                        <small class="text-muted">
                            No puedes pagar más del saldo pendiente.
                        </small>

                    </div>

                    {{-- QR ESTÁTICO --}}
                    <div class="col-12" id="seccion_qr" style="display:none;">

                        <div class="qr-card p-4 mt-2 text-center">

                            <h5 class="fw-bold">
                                <i class="bi bi-qr-code me-2"></i>
                                Pago mediante QR
                            </h5>

                            <p class="text-muted">
                                Escanea este QR desde tu aplicación bancaria.
                            </p>

                            <img src="{{ asset('img/qr.jpg') }}" alt="QR del Centro Interno"
                                class="qr-image img-fluid shadow-sm">

                            <div class="mt-3">
                                <a href="{{ asset('img/qr.jpg') }}" download="QR-Centro-Interno.jpg"
                                    class="btn btn-outline-primary btn-sm">

                                    <i class="bi bi-download me-1"></i>
                                    Descargar QR
                                </a>
                            </div>

                            <div class="alert alert-warning text-start mt-4 mb-0">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>Importante:</strong>
                                El QR es estático.
                                Verifica que el dinero haya sido recibido
                                antes de confirmar el pago.
                            </div>

                        </div>
                    </div>

                    {{-- REFERENCIA --}}
                    <div class="col-md-6">

                        <label class="form-label">
                            Número de referencia o comprobante
                        </label>

                        <input type="text" name="referencia" class="form-control" value="{{ old('referencia') }}"
                            placeholder="Número de transacción">

                    </div>

                    {{-- OBSERVACIONES --}}
                    <div class="col-md-6">

                        <label class="form-label">
                            Observaciones
                        </label>

                        <textarea name="observaciones" class="form-control" rows="2" placeholder="Observaciones del pago">{{ old('observaciones') }}</textarea>

                    </div>

                </div>
            </div>

            <div class="card-footer d-flex justify-content-end gap-2">

                <a href="{{ route('pagos.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>
                    Cancelar
                </a>

                <button type="submit" class="btn btn-success" id="btn_guardar">

                    <i class="bi bi-check-circle me-1"></i>
                    Confirmar pago verificado

                </button>

            </div>

        </form>
    </div>

@stop

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const buscador = document.getElementById('buscar_inscripcion');
            const inscripcion = document.getElementById('inscripcion_id');
            const metodo = document.getElementById('metodo_pago_id');
            const monto = document.getElementById('monto');
            const qr = document.getElementById('seccion_qr');
            const boton = document.getElementById('btn_guardar');
            const mensaje = document.getElementById('mensaje_inscripciones');

            // GUARDAR OPCIONES ORIGINALES
            const opcionesOriginales = Array.from(
                inscripcion.options
            ).map(opcion => opcion.cloneNode(true));

            // BUSCAR ESTUDIANTES
            function filtrarInscripciones() {

                const texto = buscador.value.trim().toLowerCase();
                const seleccionado = inscripcion.value;

                inscripcion.innerHTML = '';

                let encontrados = 0;

                opcionesOriginales.forEach(function(opcion) {

                    if (
                        opcion.value === '' ||
                        opcion.textContent.toLowerCase().includes(texto)
                    ) {

                        inscripcion.appendChild(opcion.cloneNode(true));

                        if (opcion.value !== '') {
                            encontrados++;
                        }
                    }
                });

                const existeSeleccionado = Array.from(
                    inscripcion.options
                ).some(opcion => opcion.value === seleccionado);

                inscripcion.value = existeSeleccionado ?
                    seleccionado :
                    '';

                mensaje.style.display =
                    encontrados === 0 && texto !== '' ?
                    'block' :
                    'none';

                inscripcion.dispatchEvent(
                    new Event('change', {
                        bubbles: true
                    })
                );
            }

            buscador.addEventListener('input', filtrarInscripciones);

            // FORMATO MONETARIO
            function dinero(valor) {
                return 'Bs ' + Number(valor || 0).toFixed(2);
            }

            // ACTUALIZAR COSTO Y SALDO
            function actualizarSaldo() {

                const opcion = inscripcion.options[
                    inscripcion.selectedIndex
                ];

                const costo = Number(opcion?.dataset.costo || 0);
                const pagado = Number(opcion?.dataset.pagado || 0);
                const saldo = Number(opcion?.dataset.saldo || 0);

                document.getElementById('resumen_costo').textContent =
                    dinero(costo);

                document.getElementById('resumen_pagado').textContent =
                    dinero(pagado);

                document.getElementById('resumen_saldo').textContent =
                    dinero(saldo);

                monto.max = saldo.toFixed(2);

                const abono = Number(monto.value);

                boton.disabled = !inscripcion.value ||
                    saldo <= 0 ||
                    !(abono > 0 && abono <= saldo);
            }

            // MOSTRAR QR SOLO SI EL MÉTODO ES QR
            function actualizarMetodo() {

                const opcion = metodo.options[
                    metodo.selectedIndex
                ];

                const nombre = (opcion?.dataset.nombre || '')
                    .trim()
                    .toLowerCase();

                qr.style.display =
                    nombre.includes('qr') ?
                    'block' :
                    'none';
            }

            inscripcion.addEventListener('change', actualizarSaldo);
            metodo.addEventListener('change', actualizarMetodo);
            monto.addEventListener('input', actualizarSaldo);

            actualizarSaldo();
            actualizarMetodo();
        });
    </script>
@stop
