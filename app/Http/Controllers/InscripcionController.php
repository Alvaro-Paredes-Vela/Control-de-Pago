<?php

namespace App\Http\Controllers;

use App\Models\Inscripcion;
use App\Models\Estudiante;
use App\Models\Actividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class InscripcionController extends Controller
{
    // LISTAR INSCRIPCIONES
    public function index()
    {
        $inscripciones = Inscripcion::with([
            'estudiante',
            'actividad'
        ])
            ->latest()
            ->paginate(10);

        return view('inscripciones.index', compact('inscripciones'));
    }

    // FORMULARIO DE REGISTRO
    public function create()
    {
        $estudiantes = Estudiante::where('estado', 'activo')
            ->orderBy('apellidos')
            ->get();

        $actividades = Actividad::with('campos')
            ->where('estado', 'inscripciones_abiertas')
            ->orderBy('nombre')
            ->get();

        return view('inscripciones.create', compact(
            'estudiantes',
            'actividades'
        ));
    }

    // GUARDAR INSCRIPCIÓN
    public function store(Request $request)
    {
        // Validar datos principales
        $datos = $request->validate([
            'estudiante_id' => 'required|exists:estudiantes,id',
            'actividad_id' => 'required|exists:actividades,id',
            'observaciones' => 'nullable|string',
            'respuestas' => 'nullable|array',
        ]);

        DB::transaction(function () use ($request, $datos) {

            // Verificar estudiante
            $estudiante = Estudiante::findOrFail(
                $datos['estudiante_id']
            );

            if ($estudiante->estado !== 'activo') {
                throw ValidationException::withMessages([
                    'estudiante_id' => 'El estudiante está inactivo.'
                ]);
            }

            // Bloquear actividad para controlar cupos
            $actividad = Actividad::whereKey(
                $datos['actividad_id']
            )
                ->lockForUpdate()
                ->firstOrFail();

            if ($actividad->estado !== 'inscripciones_abiertas') {
                throw ValidationException::withMessages([
                    'actividad_id' => 'Las inscripciones no están abiertas.'
                ]);
            }

            // Evitar inscripciones duplicadas
            $existe = Inscripcion::where(
                'estudiante_id',
                $estudiante->id
            )
                ->where('actividad_id', $actividad->id)
                ->exists();

            if ($existe) {
                throw ValidationException::withMessages([
                    'estudiante_id' =>
                    'El estudiante ya está inscrito en esta actividad.'
                ]);
            }

            // Verificar cupos disponibles
            $inscritos = Inscripcion::where(
                'actividad_id',
                $actividad->id
            )
                ->where('estado', 'inscrito')
                ->count();

            if (
                $actividad->cupo_maximo !== null &&
                $inscritos >= $actividad->cupo_maximo
            ) {
                throw ValidationException::withMessages([
                    'actividad_id' => 'No quedan cupos disponibles.'
                ]);
            }

            // Obtener campos personalizados
            $campos = $actividad->campos;

            // Construir reglas dinámicas
            $reglas = [];
            $atributos = [];

            foreach ($campos as $campo) {

                $regla = [
                    $campo->obligatorio ? 'required' : 'nullable'
                ];

                switch ($campo->tipo) {

                    case 'numero':
                        $regla[] = 'numeric';
                        break;

                    case 'fecha':
                        $regla[] = 'date_format:Y-m-d';
                        break;

                    case 'seleccion':
                        $regla[] = Rule::in(
                            $campo->opciones ?? []
                        );
                        break;

                    case 'booleano':
                        $regla[] = 'boolean';
                        break;

                    case 'texto_largo':
                        $regla[] = 'string';
                        $regla[] = 'max:5000';
                        break;

                    default:
                        $regla[] = 'string';
                        $regla[] = 'max:255';
                        break;
                }

                $reglas["respuestas.{$campo->id}"] = $regla;

                $atributos["respuestas.{$campo->id}"] =
                    $campo->etiqueta;
            }

            // Validar respuestas personalizadas
            $respuestasValidadas = Validator::make(
                $request->all(),
                array_merge(
                    ['respuestas' => 'nullable|array'],
                    $reglas
                ),
                [],
                $atributos
            )->validate();

            $respuestas = $respuestasValidadas['respuestas'] ?? [];

            // Guardar inscripción
            $inscripcion = Inscripcion::create([
                'estudiante_id' => $estudiante->id,
                'actividad_id' => $actividad->id,
                'costo_acordado' => $actividad->costo,
                'estado' => 'inscrito',
                'observaciones' => $datos['observaciones'] ?? null,
            ]);

            // Guardar respuestas de los campos personalizados
            foreach ($campos as $campo) {

                $valor = $respuestas[$campo->id] ?? null;

                if ($valor === null || $valor === '') {
                    continue;
                }

                $inscripcion->respuestas()->create([
                    'campo_id' => $campo->id,
                    'valor' => (string) $valor,
                ]);
            }
        });

        return redirect()
            ->route('inscripciones.index')
            ->with('success', 'Inscripción registrada correctamente.');
    }

    // MOSTRAR DETALLE
    public function show(Inscripcion $inscripcion)
    {
        $esFinanciero = in_array(Auth::user()?->rol, [
            'administrador',
            'tesorero'
        ], true);

        $relaciones = [
            'estudiante',
            'actividad',
            'respuestas.campo'
        ];

        // Solo administrador y tesorero pueden consultar pagos
        if ($esFinanciero) {
            $relaciones[] = 'pagos';
        }

        $inscripcion->load($relaciones);

        return view('inscripciones.show', [
            'inscripcion' => $inscripcion,
            'esFinanciero' => $esFinanciero
        ]);
    }

    // FORMULARIO DE EDICIÓN
    public function edit(Inscripcion $inscripcion)
    {
        $inscripcion->load([
            'estudiante',
            'actividad',
            'respuestas.campo'
        ]);

        return view('inscripciones.edit', [
            'inscripcion' => $inscripcion
        ]);
    }

    // ACTUALIZAR INSCRIPCIÓN
    public function update(Request $request, Inscripcion $inscripcion)
    {
        $datos = $request->validate([
            'observaciones' => 'nullable|string',
        ]);

        $inscripcion->update($datos);

        return redirect()
            ->route('inscripciones.index')
            ->with('success', 'Inscripción actualizada correctamente.');
    }

    // CANCELAR INSCRIPCIÓN
    public function destroy(Inscripcion $inscripcion)
    {
        DB::transaction(function () use ($inscripcion) {

            $inscripcion = Inscripcion::whereKey(
                $inscripcion->id
            )
                ->lockForUpdate()
                ->firstOrFail();

            // Impedir cancelar inscripciones con pagos confirmados
            if (
                $inscripcion->pagos()
                ->where('estado', 'confirmado')
                ->exists()
            ) {
                throw ValidationException::withMessages([
                    'inscripcion' =>
                    'No se puede cancelar una inscripción con pagos confirmados.'
                ]);
            }

            $inscripcion->update([
                'estado' => 'cancelado'
            ]);
        });

        return redirect()
            ->route('inscripciones.index')
            ->with('success', 'Inscripción cancelada correctamente.');
    }
}
