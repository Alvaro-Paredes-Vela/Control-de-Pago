<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActividadController extends Controller
{
    public function index()
    {
        $actividades = Actividad::with('categoria')
            ->orderBy('fecha_inicio', 'desc')
            ->paginate(10);

        return view('actividades.index', compact('actividades'));
    }

    public function create()
    {
        $categorias = Categoria::orderBy('nombre')->get();

        return view('actividades.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'lugar' => 'nullable|string|max:200',
            'costo' => 'required|numeric|min:0',
            'cupo_maximo' => 'nullable|integer|min:1',
        ]);

        $datos['creado_por'] = Auth::id();

        Actividad::create($datos);

        return redirect()->route('actividades.index')
            ->with('success', 'Actividad registrada correctamente.');
    }

    public function show(Actividad $actividad)
    {
        $actividad->load([
            'categoria',
            'inscripciones.estudiante'
        ]);

        return view('actividades.show', [
            'actividad' => $actividad
        ]);
    }


    public function edit(Actividad $actividad)
    {
        $actividad->load('campos');

        $categorias = Categoria::orderBy('nombre')->get();

        return view('actividades.edit', [
            'actividad' => $actividad,
            'categorias' => $categorias
        ]);
    }


    public function update(Request $request, Actividad $actividad)
    {
        $datos = $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'lugar' => 'nullable|string|max:200',
            'costo' => 'required|numeric|min:0',
            'cupo_maximo' => 'nullable|integer|min:1',
            'estado' => 'required|in:planificada,inscripciones_abiertas,finalizada,cancelada',
        ]);

        $inscritos = $actividad->inscripciones()
            ->where('estado', 'inscrito')
            ->count();

        if (
            !empty($datos['cupo_maximo'])
            && $datos['cupo_maximo'] < $inscritos
        ) {
            return back()->withErrors([
                'cupo_maximo' => 'El cupo no puede ser menor a los inscritos actuales.'
            ]);
        }

        $actividad->update($datos);

        return redirect()->route('actividades.index')
            ->with('success', 'Actividad actualizada correctamente.');
    }

    public function destroy(Actividad $actividad)
    {
        if ($actividad->inscripciones()->exists()) {
            return back()->with(
                'error',
                'No puedes eliminar una actividad con inscripciones.'
            );
        }

        $actividad->delete();

        return redirect()->route('actividades.index')
            ->with('success', 'Actividad eliminada correctamente.');
    }
}
