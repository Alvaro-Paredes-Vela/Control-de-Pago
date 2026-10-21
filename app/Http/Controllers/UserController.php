<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;

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

        // Los pendientes (aprobado = false) aparecen primero.
        $usuarios = User::orderBy('aprobado')
            ->orderByDesc('id')
            ->paginate(10);

        // Total de cuentas esperando aprobación (para el aviso de la vista).
        $pendientes = User::where('aprobado', false)->count();

        return view('usuarios.index', compact('usuarios', 'pendientes'));
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
            'aprobado' => true,
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

    // Muestra a quién se va a eliminar y pide confirmación.
    public function confirmarEliminar(User $usuario)
    {
        $this->verificarAdministrador();

        if ($usuario->id === Auth::id()) {
            return redirect()->route('usuarios.index')
                ->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        return view('usuarios.eliminar', compact('usuario'));
    }

    // Elimina la cuenta por completo (sin vuelta atrás).
    // Elimina la cuenta por completo (sin vuelta atrás).
    public function destroy(Request $request, User $usuario)
    {
        $this->verificarAdministrador();

        if ($usuario->id === Auth::id()) {
            return redirect()->route('usuarios.index')
                ->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        // El administrador debe escribir el correo exacto para confirmar.
        if ($request->input('correo_confirmacion') !== $usuario->email) {
            return back()->withErrors([
                'correo_confirmacion' => 'El correo escrito no coincide con el del usuario.',
            ]);
        }

        $nombre = $usuario->name;

        try {
            $usuario->delete();
        } catch (QueryException $e) {
            return redirect()->route('usuarios.index')->with(
                'error',
                "No se pudo eliminar a {$nombre}: tiene registros asociados en el sistema."
            );
        }

        // Guardamos constancia de la eliminación.
        $this->registrarEliminado($usuario, Auth::user());

        return redirect()->route('usuarios.index')
            ->with('success', "La cuenta de {$nombre} fue eliminada por completo.");
    }

    // Lista de cuentas eliminadas (más recientes primero).
    public function eliminados()
    {
        $this->verificarAdministrador();

        $eliminados = $this->leerEliminados();

        return view('usuarios.eliminados', compact('eliminados'));
    }

    // Lee el historial desde el archivo JSON.
    private function leerEliminados()
    {
        $archivo = 'usuarios_eliminados.json';

        if (! Storage::disk('local')->exists($archivo)) {
            return collect();
        }

        $datos = json_decode(Storage::disk('local')->get($archivo), true);

        return collect(is_array($datos) ? $datos : [])
            ->sortByDesc('eliminado_en')
            ->values();
    }

    // Agrega un registro al historial (sin guardar la contraseña).
    private function registrarEliminado(User $usuario, User $admin): void
    {
        $archivo = 'usuarios_eliminados.json';

        $historial = Storage::disk('local')->exists($archivo)
            ? (json_decode(Storage::disk('local')->get($archivo), true) ?: [])
            : [];

        $historial[] = [
            'id'            => $usuario->id,
            'name'          => $usuario->name,
            'email'         => $usuario->email,
            'rol'           => $usuario->rol,
            'aprobado'      => (bool) $usuario->aprobado,
            'registrado_en' => $usuario->created_at?->toDateTimeString(),
            'eliminado_en'  => now()->toDateTimeString(),
            'eliminado_por' => $admin->name,
        ];

        Storage::disk('local')->put(
            $archivo,
            json_encode($historial, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }

    // Aprueba una cuenta registrada para que pueda iniciar sesión.
    public function aprobar(User $usuario)
    {
        $this->verificarAdministrador();

        $usuario->update(['aprobado' => true]);

        return back()->with('success', "La cuenta de {$usuario->name} fue aprobada.");
    }
}
