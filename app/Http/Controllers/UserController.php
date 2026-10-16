<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct()
    {
        // Los permisos se comprueban en cada operación.
    }

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

        $usuarios = User::orderBy('id', 'desc')->paginate(10);

        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        $this->verificarAdministrador();

        return view('usuarios.create');
    }

    public function store(Request $request)
    {
        $this->verificarAdministrador();

        $datos = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $datos['name'],
            'email' => $datos['email'],
            'password' => Hash::make($datos['password']),
            'rol' => 'organizador',
        ]);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario registrado correctamente.');
    }

    public function show(User $usuario)
    {
        $this->verificarAdministrador();

        return view('usuarios.show', compact('usuario'));
    }

    public function edit(User $usuario)
    {
        $this->verificarAdministrador();

        return view('usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, User $usuario)
    {
        $this->verificarAdministrador();

        $datos = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($usuario->id),
            ],
            'rol' => 'required|in:administrador,tesorero,organizador',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if (
            $usuario->id === Auth::id()
            && $datos['rol'] !== 'administrador'
        ) {
            return back()->withErrors([
                'rol' => 'No puedes quitarte tu propio rol de administrador.'
            ]);
        }

        if (empty($datos['password'])) {
            unset($datos['password']);
        } else {
            $datos['password'] = Hash::make($datos['password']);
        }

        $usuario->update($datos);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $usuario)
    {
        $this->verificarAdministrador();

        if ($usuario->id === Auth::id()) {
            return back()->with(
                'error',
                'No puedes eliminar tu propia cuenta.'
            );
        }

        // Conservamos cuentas utilizadas en registros financieros.
        return back()->with(
            'error',
            'Por seguridad, desactiva usuarios en lugar de eliminarlos.'
        );
    }
}
