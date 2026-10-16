<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\User;


class RegisterController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $datos = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => [
                'required',
                'confirmed',
                Password::min(8),
            ],
        ]);

        $user = Cache::lock('registro_usuarios', 10)
            ->block(5, function () use ($datos) {

                // Primer usuario: administrador
                // Usuarios posteriores: organizador
                $rol = User::query()->exists() ? 'organizador' : 'administrador';

                return User::create([
                    'name' => $datos['name'],
                    'email' => $datos['email'],
                    'password' => Hash::make($datos['password']),
                    'rol' => $rol,
                ]);
            });

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }
}
