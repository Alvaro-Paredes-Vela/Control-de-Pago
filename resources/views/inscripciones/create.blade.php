{{-- resources/views/inscripciones/create.blade.php --}}

@extends('adminlte::page')

@section('title', 'Nueva inscripción')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/admin-brand.css') }}?v=4{{ '' }}">
@endpush

@section('content_header')
    <h1 class="fw-bold">
        <i class="bi bi-person-plus me-2"></i>
        Registrar inscripción
    </h1>
@stop

@section('content')

    <div class="card border-0 shadow-sm">

        <div class="card-header">
            <h3 class="card-title">Datos de la inscripción</h3>
        </div>

        <form action="{{ route('inscripciones.store') }}" method="POST">
            @csrf

            <div class="card-body">

                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Busca al estudiante por registro universitario,
                    apellido o nombre. Después selecciona la actividad.
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

                    {{-- BUSCADOR DE ESTUDIANTES --}}
                    <div class="col-md-6">

                        <label class="form-label">
                            Buscar estudiante *
                        </label>

                        <div class="input-group mb-2">
                            <span class="input-group-text">
                                <i class="bi bi-search"></i>
                            </span>

                            <input type="text" id="buscar_estudiante" class="form-control"
                                placeholder="Registro, apellido o nombre..." autocomplete="off">
                        </div>

                        <select name="estudiante_id" id="estudiante_id" class="form-select" required>

                            <option value="">
                                Seleccione un estudiante
                            </option>

                            @foreach ($estudiantes as $estudiante)
                                <option value="{{ $estudiante->id }}" @selected(old('estudiante_id') == $estudiante->id)>

                                    {{ $estudiante->registro_universitario }}
                                    - {{ $estudiante->apellidos }}
                                    {{ $estudiante->nombres }}

                                </option>
                            @endforeach
                        </select>

                        <small class="text-muted">
                            Escribe arriba para filtrar la lista.
                        </small>

                        <div id="mensaje_estudiantes" class="text-danger small mt-1" style="display:none;">
                            No se encontraron estudiantes.
                        </div>
                    </div>

                    {{-- ACTIVIDAD --}}
                    <div class="col-md-6">

                        <label class="form-label">
                            Actividad *
                        </label>

                        <select name="actividad_id" id="actividad_id" class="form-select" required>

                            <option value="">
                                Seleccione una actividad
                            </option>

                            @foreach ($actividades as $actividad)
                                <option value="{{ $actividad->id }}" data-costo="{{ $actividad->costo }}"
                                    @selected(old('actividad_id') == $actividad->id)>

                                    {{ $actividad->nombre }}

                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- COSTO DE LA ACTIVIDAD --}}
                    <div class="col-md-6">

                        <label class="form-label">
                            Costo de la actividad
                        </label>

                        <div class="input-group">
                            <span class="input-group-text">Bs</span>

                            <input type="text" id="costo_actividad" class="form-control" value="0.00" readonly>
                        </div>

                        <small class="text-muted">
                            El costo se asigna automáticamente.
                        </small>
                    </div>

                    {{-- CAMPOS PERSONALIZADOS --}}
                    <div class="col-12" id="campos_dinamicos">

                        @foreach ($actividades as $actividad)
                            <div class="formulario-actividad" data-actividad="{{ $actividad->id }}" style="display:none;">

                                @if ($actividad->campos->isNotEmpty())
                                    <div class="card border-primary mt-3 mb-0">

                                        <div class="card-header">
                                            <h5 class="mb-0">
                                                <i class="bi bi-ui-checks me-2"></i>
                                                Datos adicionales:
                                                {{ $actividad->nombre }}
                                            </h5>
                                        </div>

                                        <div class="card-body">

                                            <div class="row g-3">

                                                @foreach ($actividad->campos as $campo)
                                                    <div class="col-md-6">

                                                        <label class="form-label">
                                                            {{ $campo->etiqueta }}

                                                            @if ($campo->obligatorio)
                                                                <span class="text-danger">*</span>
                                                            @endif
                                                        </label>

                                                        @if ($campo->tipo === 'seleccion')
                                                            <select name="respuestas[{{ $campo->id }}]"
                                                                class="form-select"
                                                                data-obligatorio="{{ $campo->obligatorio ? '1' : '0' }}"
                                                                disabled>

                                                                <option value="">
                                                                    Seleccione...
                                                                </option>

                                                                @foreach ($campo->opciones ?? [] as $opcion)
                                                                    <option value="{{ $opcion }}"
                                                                        @selected(old('respuestas.' . $campo->id) == $opcion)>

                                                                        {{ $opcion }}

                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        @elseif ($campo->tipo === 'texto_largo')
                                                            <textarea name="respuestas[{{ $campo->id }}]" class="form-control" rows="3"
                                                                data-obligatorio="{{ $campo->obligatorio ? '1' : '0' }}" disabled>{{ old('respuestas.' . $campo->id) }}</textarea>
                                                        @elseif ($campo->tipo === 'booleano')
                                                            <select name="respuestas[{{ $campo->id }}]"
                                                                class="form-select"
                                                                data-obligatorio="{{ $campo->obligatorio ? '1' : '0' }}"
                                                                disabled>

                                                                <option value="">Seleccione...</option>

                                                                <option value="1" @selected(old('respuestas.' . $campo->id) === '1')>
                                                                    Sí
                                                                </option>

                                                                <option value="0" @selected(old('respuestas.' . $campo->id) === '0')>
                                                                    No
                                                                </option>
                                                            </select>
                                                        @else
                                                            <input
                                                                type="{{ match ($campo->tipo) {
                                                                    'numero' => 'number',
                                                                    'fecha' => 'date',
                                                                    default => 'text',
                                                                } }}"
                                                                name="respuestas[{{ $campo->id }}]" class="form-control"
                                                                value="{{ old('respuestas.' . $campo->id) }}"
                                                                data-obligatorio="{{ $campo->obligatorio ? '1' : '0' }}"
                                                                @if ($campo->tipo === 'numero') step="any" @endif
                                                                disabled>
                                                        @endif

                                                        @error('respuestas.' . $campo->id)
                                                            <small class="text-danger">
                                                                {{ $message }}
                                                            </small>
                                                        @enderror

                                                    </div>
                                                @endforeach

                                            </div>

                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach

                    </div>

                    {{-- OBSERVACIONES --}}
                    <div class="col-12">

                        <label class="form-label">
                            Observaciones
                        </label>

                        <textarea name="observaciones" class="form-control" rows="3" placeholder="Observaciones adicionales">{{ old('observaciones') }}</textarea>

                    </div>

                </div>
            </div>

            <div class="card-footer d-flex justify-content-end gap-2">

                <a href="{{ route('inscripciones.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>
                    Cancelar
                </a>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>
                    Guardar inscripción
                </button>

            </div>

        </form>
    </div>

@stop

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // BUSCADOR DE ESTUDIANTES
            const buscador = document.getElementById('buscar_estudiante');
            const selector = document.getElementById('estudiante_id');
            const mensaje = document.getElementById('mensaje_estudiantes');

            const opcionesOriginales = Array.from(
                selector.options
            ).map(opcion => opcion.cloneNode(true));

            function filtrarEstudiantes() {

                const texto = buscador.value.trim().toLowerCase();
                const seleccionado = selector.value;

                selector.innerHTML = '';

                let encontrados = 0;

                opcionesOriginales.forEach(function(opcion) {

                    if (
                        opcion.value === '' ||
                        opcion.textContent.toLowerCase().includes(texto)
                    ) {

                        selector.appendChild(opcion.cloneNode(true));

                        if (opcion.value !== '') {
                            encontrados++;
                        }
                    }
                });

                const existeSeleccionado = Array.from(
                    selector.options
                ).some(opcion => opcion.value === seleccionado);

                selector.value = existeSeleccionado ?
                    seleccionado :
                    '';

                mensaje.style.display =
                    encontrados === 0 && texto !== '' ?
                    'block' :
                    'none';
            }

            buscador.addEventListener('input', filtrarEstudiantes);

            // CAMPOS DINÁMICOS
            const actividad = document.getElementById('actividad_id');
            const costo = document.getElementById('costo_actividad');

            function actualizarFormulario() {

                const opcion = actividad.options[actividad.selectedIndex];
                const actividadId = actividad.value;

                costo.value = Number(
                    opcion?.dataset.costo || 0
                ).toFixed(2);

                document.querySelectorAll('.formulario-actividad')
                    .forEach(function(formulario) {

                        const activo =
                            formulario.dataset.actividad === actividadId;

                        formulario.style.display =
                            activo ? 'block' : 'none';

                        formulario.querySelectorAll(
                            'input, select, textarea'
                        ).forEach(function(campo) {

                            campo.disabled = !activo;

                            campo.required =
                                activo &&
                                campo.dataset.obligatorio === '1';
                        });
                    });
            }

            actividad.addEventListener('change', actualizarFormulario);

            actualizarFormulario();
        });
    </script>
@stop
