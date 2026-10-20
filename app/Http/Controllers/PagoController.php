<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Inscripcion;
use App\Models\MetodoPago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class PagoController extends Controller
{
    // Verifica qué roles pueden acceder a las operaciones de pagos.
    private function verificarPermiso(): void
    {
        abort_unless(
            in_array(Auth::user()?->rol, [
                'administrador',
                'tesorero',
                'organizador',
            ], true),
            403
        );
    }

    // Muestra el historial de pagos.
    public function index()
    {
        $this->verificarPermiso();

        $pagos = Pago::with([
            'inscripcion.estudiante',
            'inscripcion.actividad',
            'inscripcion.pagos',
            'metodoPago',
            'receptor',
        ])
            ->latest()
            ->paginate(10);

        return view('pagos.index', compact('pagos'));
    }

    // Muestra el formulario para registrar un pago.
    public function create()
    {
        $this->verificarPermiso();

        $inscripciones = Inscripcion::with([
            'estudiante',
            'actividad',
            'pagos',
        ])
            ->where('estado', 'inscrito')
            ->get();

        $metodos = MetodoPago::orderBy('nombre')->get();

        return view('pagos.create', compact(
            'inscripciones',
            'metodos'
        ));
    }

    // Registra un pago y verifica que no supere el saldo pendiente.
    public function store(Request $request)
    {
        $this->verificarPermiso();

        $datos = $request->validate([
            'inscripcion_id' => 'required|exists:inscripciones,id',
            'metodo_pago_id' => 'required|exists:metodos_pago,id',
            'monto' => 'required|numeric|decimal:0,2|gt:0',
            'referencia' => 'nullable|string|max:100',
            'observaciones' => 'nullable|string',
        ]);

        DB::transaction(function () use ($datos) {

            // Bloqueamos la inscripción durante el registro del pago.
            $inscripcion = Inscripcion::whereKey(
                $datos['inscripcion_id']
            )->lockForUpdate()->firstOrFail();

            if ($inscripcion->estado !== 'inscrito') {
                throw ValidationException::withMessages([
                    'inscripcion_id' => 'La inscripción no está activa.',
                ]);
            }

            // Sumamos los pagos confirmados de esta inscripción.
            $pagado = Pago::where(
                'inscripcion_id',
                $inscripcion->id
            )
                ->where('estado', 'confirmado')
                ->sum('monto');

            // Convertimos los importes a centavos para compararlos.
            $costoCentavos = (int) round(
                (float) $inscripcion->costo_acordado * 100
            );

            $pagadoCentavos = (int) round(
                (float) $pagado * 100
            );

            $montoCentavos = (int) round(
                (float) $datos['monto'] * 100
            );

            $pendiente = $costoCentavos - $pagadoCentavos;

            // Impedimos registrar un pago superior a la deuda.
            if ($montoCentavos > $pendiente) {
                throw ValidationException::withMessages([
                    'monto' => 'El pago supera el saldo pendiente de Bs '
                        . number_format(
                            max(0, $pendiente) / 100,
                            2
                        ),
                ]);
            }

            // Guardamos el pago confirmado.
            Pago::create([
                'inscripcion_id' => $inscripcion->id,
                'metodo_pago_id' => $datos['metodo_pago_id'],
                'recibido_por' => Auth::id(),
                'monto' => $datos['monto'],
                'fecha_pago' => now(),
                'referencia' => $datos['referencia'] ?? null,
                'observaciones' => $datos['observaciones'] ?? null,
                'estado' => 'confirmado',
            ]);
        });

        return redirect()->route('pagos.index')
            ->with('success', 'Pago registrado correctamente.');
    }

    // Muestra los detalles de un pago.
    public function show(Pago $pago)
    {
        $this->verificarPermiso();

        $pago->load([
            'inscripcion.estudiante',
            'inscripcion.actividad',
            'metodoPago',
            'receptor',
        ]);

        return view('pagos.show', compact('pago'));
    }

    // No permite modificar un pago ya registrado.
    public function edit(Pago $pago)
    {
        $this->verificarPermiso();

        return redirect()->route('pagos.show', $pago);
    }

    // Conserva la operación de actualización deshabilitada.
    public function update(Request $request, Pago $pago)
    {
        $this->verificarPermiso();

        return redirect()->route('pagos.index')
            ->with(
                'error',
                'Los pagos no pueden modificarse. Deben anularse y registrarse nuevamente.'
            );
    }

    // Solo administrador y tesorero pueden anular pagos.
    public function destroy(Pago $pago)
    {
        abort_unless(
            in_array(Auth::user()?->rol, [
                'administrador',
                'tesorero',
            ], true),
            403
        );

        DB::transaction(function () use ($pago) {

            // Bloqueamos el registro para evitar operaciones simultáneas.
            $pago = Pago::whereKey($pago->id)
                ->lockForUpdate()
                ->firstOrFail();

            // No hacemos nada si el pago ya está anulado.
            if ($pago->estado === 'anulado') {
                return;
            }

            // Anulamos el pago sin eliminar su historial.
            $pago->update([
                'estado' => 'anulado',
            ]);
        });

        return redirect()->route('pagos.index')
            ->with('success', 'Pago anulado correctamente.');
    }
}
