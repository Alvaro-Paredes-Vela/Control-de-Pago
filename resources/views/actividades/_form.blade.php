<div class="row g-3">

    <div class="col-md-8">
        <label class="form-label">Nombre de la actividad *</label>

        <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
            value="{{ old('nombre', $actividad->nombre ?? '') }}" placeholder="Ej. Conferencia de Ciberseguridad"
            maxlength="200" required>

        @error('nombre')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Categoría *</label>

        <select name="categoria_id" class="form-select @error('categoria_id') is-invalid @enderror" required>

            <option value="">Seleccione una categoría</option>

            @foreach ($categorias as $categoria)
                <option value="{{ $categoria->id }}" @selected(old('categoria_id', $actividad->categoria_id ?? '') == $categoria->id)>
                    {{ $categoria->nombre }}
                </option>
            @endforeach
        </select>

        @error('categoria_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label class="form-label">Descripción</label>

        <textarea name="descripcion" rows="3" class="form-control @error('descripcion') is-invalid @enderror"
            placeholder="Describe los objetivos y detalles de la actividad">{{ old('descripcion', $actividad->descripcion ?? '') }}</textarea>

        @error('descripcion')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Fecha y hora de inicio *</label>

        <input type="datetime-local" name="fecha_inicio"
            class="form-control @error('fecha_inicio') is-invalid @enderror"
            value="{{ old('fecha_inicio', isset($actividad) ? \Carbon\Carbon::parse($actividad->fecha_inicio)->format('Y-m-d\TH:i') : '') }}"
            required>

        @error('fecha_inicio')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Fecha y hora de finalización</label>

        <input type="datetime-local" name="fecha_fin" class="form-control @error('fecha_fin') is-invalid @enderror"
            value="{{ old('fecha_fin', isset($actividad) && $actividad->fecha_fin ? \Carbon\Carbon::parse($actividad->fecha_fin)->format('Y-m-d\TH:i') : '') }}">

        @error('fecha_fin')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Lugar</label>

        <input type="text" name="lugar" class="form-control @error('lugar') is-invalid @enderror"
            value="{{ old('lugar', $actividad->lugar ?? '') }}" placeholder="Ej. Auditorio de FINOR">

        @error('lugar')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Costo (Bs) *</label>

        <div class="input-group">
            <span class="input-group-text">Bs</span>
            <input type="number" name="costo" min="0" step="0.01"
                class="form-control @error('costo') is-invalid @enderror"
                value="{{ old('costo', $actividad->costo ?? 0) }}" required>
            @error('costo')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-3">
        <label class="form-label">Cupo máximo</label>

        <input type="number" name="cupo_maximo" min="1"
            class="form-control @error('cupo_maximo') is-invalid @enderror"
            value="{{ old('cupo_maximo', $actividad->cupo_maximo ?? '') }}" placeholder="Sin límite">

        @error('cupo_maximo')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    @if (isset($actividad))
        <div class="col-md-6">
            <label class="form-label">Estado de la actividad *</label>

            <select name="estado" class="form-select" required>
                @foreach ([
        'planificada' => 'Planificada',
        'inscripciones_abiertas' => 'Inscripciones abiertas',
        'finalizada' => 'Finalizada',
        'cancelada' => 'Cancelada',
    ] as $valor => $texto)
                    <option value="{{ $valor }}" @selected(old('estado', $actividad->estado) === $valor)>
                        {{ $texto }}
                    </option>
                @endforeach
            </select>
        </div>
    @endif

</div>
