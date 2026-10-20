<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Auth;
//use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\User;


class RegisterController extends Controller
{
    // Muestra el formulario de registro.
    public function showRegister()
    {
        return view('auth.register');
    }

    // Registra un usuario nuevo sin permitirle entrar todavía.
    public function register(Request $request)
    {
        // Validamos los datos recibidos del formulario.
        $datos = $request->validate([
            'name' => 'required|string|max:255',

            // El correo debe ser válido y no estar registrado.
            'email' => 'required|email|max:255|unique:users,email',

            // La contraseña debe tener al menos 8 caracteres
            // y coincidir con su confirmación.
            'password' => [
                'required',
                'confirmed',
                Password::min(8),
            ],
        ]);

        // Creamos la cuenta con rol de organizador.
        User::create([
            'name' => $datos['name'],
            'email' => $datos['email'],

            // Guardamos la contraseña cifrada mediante Hash.
            'password' => Hash::make($datos['password']),

            // Los registros públicos nunca serán administradores.
            'rol' => 'organizador',

            // La cuenta queda pendiente de autorización.
            'aprobado' => false,
        ]);

        // Enviamos al usuario al login sin iniciar su sesión.
        return redirect()
            ->route('login')
            ->with(
                'success',
                'Registro completado. Debes esperar la aprobación del administrador para ingresar.'
            );
    }
}
