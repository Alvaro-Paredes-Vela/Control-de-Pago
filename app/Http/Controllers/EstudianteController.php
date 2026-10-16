<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EstudianteController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->input('buscar');

        $estudiantes = Estudiante::query()
            ->when($buscar, function ($query) use ($buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('registro_universitario', 'like', "%{$buscar}%")
                        ->orWhere('nombres', 'like', "%{$buscar}%")
                        ->orWhere('apellidos', 'like', "%{$buscar}%");
                });
            })
            ->orderBy('apellidos')
            ->paginate(10)
            ->withQueryString();

        return view('estudiantes.index', compact('estudiantes'));
    }

    public function create()
    {
        return view('estudiantes.create');
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'registro_universitario' => 'required|string|max:20|unique:estudiantes',
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'celular' => 'nullable|string|max:20',
            'correo' => 'nullable|email|max:150',
            'semestre' => 'nullable|integer|min:1|max:20',
        ]);

        Estudiante::create($datos);

        return redirect()->route('estudiantes.index')
            ->with('success', 'Estudiante registrado correctamente.');
    }

    public function show(Estudiante $estudiante)
    {
        $estudiante->load('inscripciones.actividad');

        return view('estudiantes.show', compact('estudiante'));
    }

    public function edit(Estudiante $estudiante)
    {
        return view('estudiantes.edit', compact('estudiante'));
    }

    public function update(Request $request, Estudiante $estudiante)
    {
        $datos = $request->validate([
            'registro_universitario' => [
                'required',
                'string',
                'max:20',
                Rule::unique('estudiantes', 'registro_universitario')
                    ->ignore($estudiante->id),
            ],
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'celular' => 'nullable|string|max:20',
            'correo' => 'nullable|email|max:150',
            'semestre' => 'nullable|integer|min:1|max:20',
            'estado' => 'required|in:activo,inactivo',
        ]);

        $estudiante->update($datos);

        return redirect()->route('estudiantes.index')
            ->with('success', 'Estudiante actualizado correctamente.');
    }

    public function destroy(Estudiante $estudiante)
    {
        $estudiante->update(['estado' => 'inactivo']);

        return redirect()->route('estudiantes.index')
            ->with('success', 'Estudiante desactivado correctamente.');
    }
}
