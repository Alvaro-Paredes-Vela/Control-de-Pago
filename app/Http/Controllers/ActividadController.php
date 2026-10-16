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

    public function show(Actividad $actividade)
    {
        $actividade->load([
            'categoria',
            'inscripciones.estudiante'
        ]);

        return view('actividades.show', [
            'actividad' => $actividade
        ]);
    }


    public function edit(Actividad $actividade)
    {
        $actividade->load('campos');

        $categorias = Categoria::orderBy('nombre')->get();

        return view('actividades.edit', [
            'actividad' => $actividade,
            'categorias' => $categorias
        ]);
    }


    public function update(Request $request, Actividad $actividade)
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

        $inscritos = $actividade->inscripciones()
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

        $actividade->update($datos);

        return redirect()->route('actividades.index')
            ->with('success', 'Actividad actualizada correctamente.');
    }

    public function destroy(Actividad $actividade)
    {
        if ($actividade->inscripciones()->exists()) {
            return back()->with(
                'error',
                'No puedes eliminar una actividad con inscripciones.'
            );
        }

        $actividade->delete();

        return redirect()->route('actividades.index')
            ->with('success', 'Actividad eliminada correctamente.');
    }
}
