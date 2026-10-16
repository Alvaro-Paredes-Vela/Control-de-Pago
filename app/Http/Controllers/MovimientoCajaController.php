<?php

namespace App\Http\Controllers;

use App\Models\MovimientoCaja;
use App\Models\Actividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MovimientoCajaController extends Controller
{
    private function verificarPermiso(): void
    {
        abort_unless(
            in_array(Auth::user()?->rol, [
                'administrador',
                'tesorero'
            ], true),
            403
        );
    }


    public function index()
    {
        // Verificar que sea administrador o tesorero
        $this->verificarPermiso();

        // Obtener los movimientos con sus relaciones
        $movimientos = MovimientoCaja::with([
            'actividad',
            'usuario'
        ])->orderBy('fecha_movimiento', 'desc')
            ->paginate(10);

        // Calcular ingresos confirmados
        $ingresos = MovimientoCaja::where('tipo', 'ingreso')
            ->where('estado', 'confirmado')
            ->sum('monto');

        // Calcular egresos confirmados
        $egresos = MovimientoCaja::where('tipo', 'egreso')
            ->where('estado', 'confirmado')
            ->sum('monto');

        // Calcular balance de movimientos adicionales
        $balance = $ingresos - $egresos;

        // Enviar los datos a la vista
        return view('movimientos_caja.index', compact(
            'movimientos',
            'ingresos',
            'egresos',
            'balance'
        ));
    }


    public function create()
    {
        $this->verificarPermiso();

        $actividades = Actividad::orderBy('nombre')->get();

        return view('movimientos_caja.create', compact('actividades'));
    }

    public function store(Request $request)
    {
        $this->verificarPermiso();

        $datos = $request->validate([
            'actividad_id' => 'nullable|exists:actividades,id',
            'tipo' => 'required|in:ingreso,egreso',
            'concepto' => 'required|string|max:200',
            'monto' => 'required|numeric|decimal:0,2|gt:0',
            'comprobante' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string',
        ]);

        $datos['usuario_id'] = Auth::id();
        $datos['fecha_movimiento'] = now();
        $datos['estado'] = 'confirmado';

        MovimientoCaja::create($datos);

        return redirect()->route('movimientos-caja.index')
            ->with('success', 'Movimiento registrado correctamente.');
    }

    public function show(MovimientoCaja $movimientos_caja)
    {
        $this->verificarPermiso();

        $movimientos_caja->load(['actividad', 'usuario']);

        return view('movimientos_caja.show', [
            'movimiento' => $movimientos_caja
        ]);
    }

    public function edit(MovimientoCaja $movimientos_caja)
    {
        $this->verificarPermiso();

        return redirect()->route(
            'movimientos-caja.show',
            $movimientos_caja
        );
    }

    public function update(Request $request, MovimientoCaja $movimientos_caja)
    {
        $this->verificarPermiso();

        return redirect()->route('movimientos-caja.index')
            ->with('error', 'Los movimientos confirmados no pueden editarse.');
    }

    public function destroy(MovimientoCaja $movimientos_caja)
    {
        $this->verificarPermiso();

        DB::transaction(function () use ($movimientos_caja) {

            $movimiento = MovimientoCaja::whereKey(
                $movimientos_caja->id
            )->lockForUpdate()->firstOrFail();

            $movimiento->update(['estado' => 'anulado']);
        });

        return redirect()->route('movimientos-caja.index')
            ->with('success', 'Movimiento anulado correctamente.');
    }
}
