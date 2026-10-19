<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Mostrar la pantalla de inicio de sesión.
    public function showLogin()
    {
        return view('auth.login');
    }

    // Verificar las credenciales del usuario.
    public function login(Request $request)
    {
        // Validar el correo y la contraseña recibidos.
        $credenciales = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Comprobar si las credenciales son correctas.
        if (Auth::attempt($credenciales)) {

            // Regenerar la sesión por seguridad.
            $request->session()->regenerate();

            // Entrar al panel principal.
            return redirect()->intended(route('dashboard'));
        }

        // Mostrar un error si los datos son incorrectos.
        return back()
            ->withErrors([
                'email' => 'Correo o contraseña incorrectos.',
            ])
            ->onlyInput('email');
    }

    // Cerrar la sesión del usuario.
    public function logout(Request $request)
    {
        // Cerrar la sesión autenticada.
        Auth::logout();

        // Invalidar la sesión actual.
        $request->session()->invalidate();

        // Generar un nuevo token de seguridad.
        $request->session()->regenerateToken();

        // Regresar a la pantalla de inicio de sesión.
        return redirect()
            ->route('login')
            ->with('success', 'Sesión cerrada correctamente.');
    }
}
