@extends('adminlte::page')

@section('title', 'Métodos de Pago')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/admin-brand.css') }}">
@endpush

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1 class="fw-bold">Métodos de Pago</h1>
            <p class="text-muted mb-0">
                Administración de formas de pago
            </p>
        </div>

        <a href="{{ route('metodos-pago.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>
            Nuevo método
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

    <div class="card border-0 shadow-sm">
        <div class="card-header">
            <h3 class="card-title">
                <i class="bi bi-credit-card me-2"></i>
                Lista de métodos de pago
            </h3>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($metodos as $metodo)
                            <tr>
                                <td>
                                    {{ $metodos->firstItem() + $loop->index }}
                                </td>

                                <td>
                                    <i class="bi bi-credit-card text-primary me-2"></i>
                                    <strong>{{ $metodo->nombre }}</strong>
                                </td>

                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('metodos-pago.show', $metodo) }}"
                                            class="btn btn-info btn-sm text-white" title="Ver">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        <a href="{{ route('metodos-pago.edit', $metodo) }}" class="btn btn-warning btn-sm"
                                            title="Editar">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <form action="{{ route('metodos-pago.destroy', $metodo) }}" method="POST"
                                            onsubmit="return confirm('¿Eliminar este método de pago?')">
                                            @csrf
                                            @method('DELETE')

                                            <button class="btn btn-danger btn-sm" title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">
                                    No existen métodos de pago registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $metodos->links() }}
            </div>
        </div>
    </div>
@stop
