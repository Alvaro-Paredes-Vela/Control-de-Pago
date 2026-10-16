@extends('adminlte::page')

@section('title', 'Editar actividad')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/admin-brand.css') }}?v=4{{ '' }}">
@endpush

@section('content_header')
    <h1 class="fw-bold">
        <i class="bi bi-pencil-square me-2"></i>
        Editar actividad
    </h1>
@stop

@section('content')

    <div class="card border-0 shadow-sm">

        <div class="card-header">
            <h3 class="card-title">
                {{ $actividad->nombre }}
            </h3>
        </div>

        <form action="{{ route('actividades.update', $actividad) }}" method="POST">

            @csrf
            @method('PUT')

            <div class="card-body">

                @include('actividades._form')

            </div>

            <div class="card-footer d-flex justify-content-end gap-2">
                <a href="{{ route('actividades.index') }}" class="btn btn-secondary">
                    Cancelar
                </a>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>
                    Actualizar actividad
                </button>
            </div>

        </form>

    </div>
    @include('actividades._campos')
@stop
