<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;



class LoginController extends Controller
{
    // Muestra el formulario de inicio de sesión.
    public function showLogin()
    {
        return view('auth.login');
    }

    // Comprueba los datos y permite entrar solo a usuarios autorizados.
    public function login(Request $request)
    {
        // Validamos el correo y la contraseña ingresados.
        $credenciales = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Verificamos si el correo y la contraseña son correctos.
        if (Auth::attempt($credenciales)) {

            // Obtenemos los datos del usuario que inició sesión.
            $usuario = Auth::user();

            // Revisamos si el administrador aprobó su cuenta.
            if (!$usuario->aprobado) {

                // Cerramos la sesión si todavía no tiene autorización.
                Auth::logout();

                // Regresamos al formulario con un mensaje de espera.
                return back()
                    ->withErrors([
                        'email' => 'Tu cuenta está pendiente de aprobación del administrador.',
                    ])
                    ->onlyInput('email');
            }

            // Renovamos la sesión por seguridad.
            $request->session()->regenerate();

            // Enviamos al usuario autorizado al panel principal.
            return redirect()->intended(route('dashboard'));
        }

        // Mostramos un mensaje si los datos son incorrectos.
        return back()
            ->withErrors([
                'email' => 'Correo o contraseña incorrectos.',
            ])
            ->onlyInput('email');
    }

    // Cierra la sesión del usuario.
    public function logout(Request $request)
    {
        // Cerramos la sesión actual.
        Auth::logout();

        // Invalidamos la sesión anterior.
        $request->session()->invalidate();

        // Generamos un nuevo token de seguridad.
        $request->session()->regenerateToken();

        // Regresamos al inicio de sesión.
        return redirect()
            ->route('login')
            ->with('success', 'Sesión cerrada correctamente.');
    }
}
