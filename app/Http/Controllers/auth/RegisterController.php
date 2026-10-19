<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\User;


class RegisterController extends Controller
{
    // Mostrar el formulario de registro.
    public function showRegister()
    {
        return view('auth.register');
    }

    // Registrar un usuario nuevo.
    public function register(Request $request)
    {
        // Validar los datos del formulario.
        $datos = $request->validate([
            'name' => 'required|string|max:255',

            'email' => 'required|email|max:255|unique:users,email',

            'password' => [
                'required',
                'confirmed',
                Password::min(8),
            ],
        ]);

        // Bloquear temporalmente otros registros simultáneos.
        $user = Cache::lock('registro_usuarios', 10)
            ->block(5, function () use ($datos) {

                // El primer usuario será administrador.
                // Los usuarios posteriores serán organizadores.
                $rol = User::query()->exists()
                    ? 'organizador'
                    : 'administrador';

                // Crear el usuario con la contraseña protegida.
                return User::create([
                    'name' => $datos['name'],
                    'email' => $datos['email'],
                    'password' => Hash::make($datos['password']),
                    'rol' => $rol,
                ]);
            });

        // No iniciar sesión automáticamente.
        // El usuario deberá entrar desde el formulario de login.
        return redirect()
            ->route('login')
            ->with(
                'success',
                'Registro completado. Ahora puedes iniciar sesión.'
            );
    }
}
