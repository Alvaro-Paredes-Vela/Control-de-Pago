<?php

namespace App\Http\Controllers;

use App\Models\MetodoPago;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class MetodoPagoController extends Controller
{
    private function verificarAdministrador(): void
    {
        abort_unless(
            Auth::user()?->rol === 'administrador',
            403
        );
    }

    public function index()
    {
        $this->verificarAdministrador();

        $metodos = MetodoPago::orderBy('nombre')->paginate(10);

        return view('metodos_pago.index', compact('metodos'));
    }

    public function create()
    {
        $this->verificarAdministrador();

        return view('metodos_pago.create');
    }

    public function store(Request $request)
    {
        $this->verificarAdministrador();

        $datos = $request->validate([
            'nombre' => 'required|string|max:50|unique:metodos_pago,nombre',
        ]);

        MetodoPago::create($datos);

        return redirect()->route('metodos-pago.index')
            ->with('success', 'Método de pago registrado.');
    }

    public function show(MetodoPago $metodo_pago)
    {
        $this->verificarAdministrador();

        return view('metodos_pago.show', [
            'metodo' => $metodo_pago
        ]);
    }

    public function edit(MetodoPago $metodo_pago)
    {
        $this->verificarAdministrador();

        return view('metodos_pago.edit', [
            'metodo' => $metodo_pago
        ]);
    }

    public function update(Request $request, MetodoPago $metodo_pago)
    {
        $this->verificarAdministrador();

        $datos = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:50',
                Rule::unique('metodos_pago', 'nombre')
                    ->ignore($metodo_pago->id),
            ],
        ]);

        $metodo_pago->update($datos);

        return redirect()->route('metodos-pago.index')
            ->with('success', 'Método de pago actualizado.');
    }

    public function destroy(MetodoPago $metodo_pago)
    {
        $this->verificarAdministrador();

        if ($metodo_pago->pagos()->exists()) {
            return back()->with(
                'error',
                'El método de pago tiene pagos registrados.'
            );
        }

        $metodo_pago->delete();

        return redirect()->route('metodos-pago.index')
            ->with('success', 'Método de pago eliminado.');
    }
}
