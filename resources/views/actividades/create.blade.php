@extends('adminlte::page')

@section('title', 'Nueva actividad')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/admin-brand.css') }}">
@endpush

@section('content_header')
    <h1 class="fw-bold">
        <i class="bi bi-calendar-plus me-2"></i>
        Registrar actividad
    </h1>
@stop

@section('content')

    <div class="card border-0 shadow-sm">

        <div class="card-header">
            <h3 class="card-title">
                Datos de la nueva actividad
            </h3>
        </div>

        <form action="{{ route('actividades.store') }}" method="POST">
            @csrf

            <div class="card-body">

                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Los campos marcados con * son obligatorios.
                    Para una actividad gratuita, coloca costo 0.
                </div>

                @include('actividades._form')

            </div>

            <div class="card-footer d-flex justify-content-end gap-2">
                <a href="{{ route('actividades.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>
                    Cancelar
                </a>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>
                    Guardar actividad
                </button>
            </div>
        </form>

    </div>
@stop
