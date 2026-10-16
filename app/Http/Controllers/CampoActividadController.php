<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Actividad;
use App\Models\CampoActividad;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CampoActividadController extends Controller
{
    public function store(Request $request, Actividad $actividad)
    {
        $datos = $request->validate([
            'etiqueta' => 'required|string|max:150',
            'tipo' => [
                'required',
                Rule::in([
                    'texto',
                    'numero',
                    'fecha',
                    'seleccion',
                    'texto_largo',
                    'booleano'
                ])
            ],
            'obligatorio' => 'nullable|boolean',
            'opciones' => 'nullable|string|max:2000',
        ]);

        $opciones = null;

        if ($datos['tipo'] === 'seleccion') {
            $opciones = collect(explode(',', $datos['opciones'] ?? ''))
                ->map(fn($item) => trim($item))
                ->filter()
                ->unique()
                ->values()
                ->all();

            if (count($opciones) < 2) {
                throw ValidationException::withMessages([
                    'opciones' => 'Agrega al menos dos opciones separadas por comas.'
                ]);
            }
        }

        $nombreBase = Str::slug($datos['etiqueta'], '_');

        if ($nombreBase === '') {
            $nombreBase = 'campo';
        }

        $nombre = $nombreBase;
        $numero = 2;

        while ($actividad->campos()
            ->where('nombre', $nombre)->exists()
        ) {
            $nombre = $nombreBase . '_' . $numero++;
        }

        $actividad->campos()->create([
            'nombre' => $nombre,
            'etiqueta' => $datos['etiqueta'],
            'tipo' => $datos['tipo'],
            'opciones' => $opciones,
            'obligatorio' => $request->boolean('obligatorio'),
            'orden' => ($actividad->campos()->max('orden') ?? 0) + 1,
        ]);

        return redirect()
            ->route('actividades.edit', $actividad)
            ->with('success', 'Campo personalizado agregado.');
    }

    public function destroy(
        Actividad $actividad,
        CampoActividad $campo
    ) {
        abort_unless(
            $campo->actividad_id === $actividad->id,
            404
        );

        if ($campo->respuestas()->exists()) {
            return back()->with(
                'error',
                'Este campo ya tiene respuestas y no puede eliminarse.'
            );
        }

        $campo->delete();

        return back()->with('success', 'Campo eliminado.');
    }
}
