@extends('adminlte::page')

@section('title', 'Usuarios eliminados')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/admin-brand.css') }}?v=4{{ '' }}">
@endpush

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1 class="fw-bold">Usuarios eliminados</h1>
            <p class="text-muted mb-0">
                Historial de cuentas borradas del sistema
            </p>
        </div>

        <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Volver a usuarios
        </a>
    </div>
@stop

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-header">
            <h3 class="card-title">
                <i class="bi bi-person-x me-2"></i>
                Cuentas eliminadas
                <span class="badge text-bg-danger ms-2">{{ $eliminados->count() }}</span>
            </h3>
        </div>

        <div class="card-body">
            @php
                $colores = [
                    'administrador' => 'danger',
                    'tesorero' => 'success',
                    'organizador' => 'primary',
                ];
            @endphp

            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th>Correo electrónico</th>
                            <th>Rol</th>
                            <th>Estado al eliminar</th>
                            <th>Registrado</th>
                            <th>Eliminado</th>
                            <th>Eliminado por</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($eliminados as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>

                                <td>
                                    <i class="bi bi-person-circle text-secondary me-2"></i>
                                    <strong>{{ $item['name'] }}</strong>
                                </td>

                                <td>{{ $item['email'] }}</td>

                                <td>
                                    <span class="badge bg-{{ $colores[$item['rol']] ?? 'secondary' }}">
                                        {{ ucfirst($item['rol']) }}
                                    </span>
                                </td>

                                <td>
                                    @if ($item['aprobado'])
                                        <span class="badge text-bg-success">Aprobado</span>
                                    @else
                                        <span class="badge text-bg-warning">Pendiente</span>
                                    @endif
                                </td>

                                <td>
                                    {{ $item['registrado_en'] ? \Carbon\Carbon::parse($item['registrado_en'])->format('d/m/Y') : '—' }}
                                </td>

                                <td>
                                    {{ \Carbon\Carbon::parse($item['eliminado_en'])->format('d/m/Y H:i') }}
                                </td>

                                <td>{{ $item['eliminado_por'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    Aún no hay cuentas eliminadas registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop
