<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Inscripcion;
use App\Models\Actividad;
use Illuminate\Support\Facades\Auth;

class EstadoCuentaController extends Controller
{
    /**
     * Mostrar el estado de cuentas de los estudiantes.
     */
    public function index(Request $request)
    {
        // Solo el administrador y el tesorero pueden consultar las cuentas.
        abort_unless(
            in_array(Auth::user()?->rol, ['administrador', 'tesorero'], true),
            403
        );

        // Obtener el texto que escribió el usuario en el buscador.
        $buscar = trim((string) $request->query('buscar', ''));

        // Obtener la actividad seleccionada en el filtro.
        $actividadId = $request->query('actividad_id');

        // Consultar las inscripciones y calcular los pagos confirmados.
        $inscripciones = Inscripcion::query()
            ->with([
                'estudiante',
                'actividad',
            ])

            // Sumar únicamente los pagos que están confirmados.
            ->withSum([
                'pagos as total_pagado' => function ($query) {
                    $query->where('estado', 'confirmado');
                }
            ], 'monto')

            // Mostrar únicamente inscripciones activas.
            ->where('estado', 'inscrito')

            // Buscar por registro universitario, nombres o apellidos.
            ->when($buscar !== '', function ($query) use ($buscar) {
                $query->whereHas('estudiante', function ($estudiante) use ($buscar) {
                    $estudiante->where(function ($filtro) use ($buscar) {
                        $filtro->where(
                            'registro_universitario',
                            'like',
                            "%{$buscar}%"
                        )
                            ->orWhere('nombres', 'like', "%{$buscar}%")
                            ->orWhere('apellidos', 'like', "%{$buscar}%");
                    });
                });
            })

            // Filtrar por actividad si el usuario seleccionó una.
            ->when(
                $request->filled('actividad_id'),
                function ($query) use ($actividadId) {
                    $query->where('actividad_id', $actividadId);
                }
            )

            // Mostrar primero las inscripciones más recientes.
            ->latest()

            // Mostrar 15 cuentas por página y conservar los filtros.
            ->paginate(15)
            ->withQueryString();

        // Obtener las actividades para llenar el selector de la vista.
        $actividades = Actividad::orderBy('nombre')->get([
            'id',
            'nombre',
        ]);

        // Enviar los datos a la vista estado-cuentas/index.blade.php.
        return view('estado-cuentas.index', compact(
            'inscripciones',
            'actividades',
            'buscar',
            'actividadId'
        ));
    }
}
