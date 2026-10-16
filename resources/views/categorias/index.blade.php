@extends('adminlte::page')

@section('title', 'Categorías')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/admin-brand.css') }}?v=4{{ '' }}">
@endpush

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1 class="fw-bold">Categorías de actividades</h1>
            <p class="text-muted mb-0">
                Administración de tipos de actividades
            </p>
        </div>

        <a href="{{ route('categorias.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>
            Nueva categoría
        </a>
    </div>
@stop

@section('content')

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($categorias as $categoria)
                            <tr>
                                <td>
                                    {{ $categorias->firstItem() + $loop->index }}
                                </td>

                                <td>
                                    <strong>{{ $categoria->nombre }}</strong>
                                </td>

                                <td>
                                    {{ $categoria->descripcion ?? 'Sin descripción' }}
                                </td>

                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('categorias.show', $categoria) }}"
                                            class="btn btn-info btn-sm text-white">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        <a href="{{ route('categorias.edit', $categoria) }}" class="btn btn-warning btn-sm">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <form method="POST" action="{{ route('categorias.destroy', $categoria) }}"
                                            onsubmit="return confirm('¿Eliminar esta categoría?')">
                                            @csrf
                                            @method('DELETE')

                                            <button class="btn btn-danger btn-sm">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    No existen categorías registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $categorias->links() }}
            </div>
        </div>
    </div>
@stop
