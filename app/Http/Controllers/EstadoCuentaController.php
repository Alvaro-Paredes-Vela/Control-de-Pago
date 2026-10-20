<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Inscripcion;
use App\Models\Actividad;
use Illuminate\Support\Facades\Auth;

class EstadoCuentaController extends Controller
{
    // Muestra el estado de cuenta de los estudiantes.
    public function index(Request $request)
    {
        // Permite consultar las cuentas a los tres roles.
        abort_unless(
            in_array(Auth::user()?->rol, [
                'administrador',
                'tesorero',
                'organizador',
            ], true),
            403
        );

        // Obtenemos el texto ingresado en el buscador.
        $buscar = trim((string) $request->query('buscar', ''));

        // Obtenemos la actividad seleccionada.
        $actividadId = $request->query('actividad_id');

        // Consultamos inscripciones y calculamos los pagos confirmados.
        $inscripciones = Inscripcion::query()
            ->with([
                'estudiante',
                'actividad',
            ])

            // Sumamos únicamente los pagos confirmados.
            ->withSum([
                'pagos as total_pagado' => function ($query) {
                    $query->where('estado', 'confirmado');
                },
            ], 'monto')

            // Mostramos solamente las inscripciones activas.
            ->where('estado', 'inscrito')

            // Buscamos por registro universitario, nombres o apellidos.
            ->when($buscar !== '', function ($query) use ($buscar) {
                $query->whereHas(
                    'estudiante',
                    function ($estudiante) use ($buscar) {
                        $estudiante->where(function ($filtro) use ($buscar) {
                            $filtro->where(
                                'registro_universitario',
                                'like',
                                "%{$buscar}%"
                            )
                                ->orWhere('nombres', 'like', "%{$buscar}%")
                                ->orWhere('apellidos', 'like', "%{$buscar}%");
                        });
                    }
                );
            })

            // Filtramos por actividad cuando se selecciona una.
            ->when(
                $request->filled('actividad_id'),
                function ($query) use ($actividadId) {
                    $query->where(
                        'actividad_id',
                        $actividadId
                    );
                }
            )

            // Ordenamos desde las inscripciones más recientes.
            ->latest()

            // Mostramos 15 registros por página.
            ->paginate(15)
            ->withQueryString();

        // Obtenemos las actividades para el filtro de la vista.
        $actividades = Actividad::orderBy('nombre')->get([
            'id',
            'nombre',
        ]);

        // Enviamos los datos a la vista.
        return view('estado-cuentas.index', compact(
            'inscripciones',
            'actividades',
            'buscar',
            'actividadId'
        ));
    }
}
