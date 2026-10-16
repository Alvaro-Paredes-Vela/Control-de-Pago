<div class="card border-0 shadow-sm mt-4">

    <div class="card-header">
        <h3 class="card-title">
            <i class="bi bi-ui-checks me-2"></i>
            Campos personalizados
        </h3>
    </div>

    <div class="card-body">

        <p class="text-muted">
            Configura los datos adicionales que se pedirán
            a los estudiantes al inscribirse.
        </p>

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

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- CAMPOS EXISTENTES --}}
        <div class="table-responsive mb-4">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Campo</th>
                        <th>Tipo</th>
                        <th>Obligatorio</th>
                        <th>Opciones</th>
                        <th>Acción</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($actividad->campos as $campo)
                        <tr>
                            <td>{{ $campo->etiqueta }}</td>

                            <td>
                                {{ ucfirst(str_replace('_', ' ', $campo->tipo)) }}
                            </td>

                            <td>
                                @if ($campo->obligatorio)
                                    <span class="badge bg-danger">Sí</span>
                                @else
                                    <span class="badge bg-secondary">No</span>
                                @endif
                            </td>

                            <td>
                                {{ implode(', ', $campo->opciones ?? []) ?: '—' }}
                            </td>

                            <td>
                                <form method="POST"
                                    action="{{ route('actividades.campos.destroy', [$actividad, $campo]) }}"
                                    onsubmit="return confirm('¿Eliminar este campo?')">
                                    @csrf
                                    @method('DELETE')

                                    <button class="btn btn-danger btn-sm">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">
                                No hay campos personalizados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <hr>

        <h5 class="fw-bold mb-3">
            <i class="bi bi-plus-circle me-2"></i>
            Agregar nuevo campo
        </h5>

        <form method="POST" action="{{ route('actividades.campos.store', $actividad) }}">
            @csrf

            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">Nombre del campo *</label>

                    <input type="text" name="etiqueta" class="form-control" placeholder="Ej. Talla, color, equipo"
                        value="{{ old('etiqueta') }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Tipo de campo *</label>

                    <select name="tipo" id="tipo_campo" class="form-select" required>
                        <option value="texto">Texto</option>
                        <option value="numero">Número</option>
                        <option value="fecha">Fecha</option>
                        <option value="seleccion">Lista desplegable</option>
                        <option value="texto_largo">Texto largo</option>
                        <option value="booleano">Sí / No</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label d-block">Obligatorio</label>

                    <div class="form-check mt-2">
                        <input type="checkbox" name="obligatorio" value="1" class="form-check-input"
                            id="obligatorio">

                        <label class="form-check-label" for="obligatorio">
                            Sí
                        </label>
                    </div>
                </div>

                <div class="col-12" id="bloque_opciones" style="display:none;">
                    <label class="form-label">Opciones disponibles *</label>

                    <input type="text" name="opciones" class="form-control" placeholder="Ej. S, M, L, XL, XXL">

                    <small class="text-muted">
                        Separa cada opción utilizando comas.
                    </small>
                </div>

                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>
                        Agregar campo
                    </button>
                </div>

            </div>
        </form>

    </div>
</div>

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tipo = document.getElementById('tipo_campo');
            const bloque = document.getElementById('bloque_opciones');

            function actualizarOpciones() {
                bloque.style.display =
                    tipo.value === 'seleccion' ? 'block' : 'none';
            }

            tipo.addEventListener('change', actualizarOpciones);
            actualizarOpciones();
        });
    </script>
@endpush
