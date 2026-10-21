<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'rol',
        'aprobado'
    ];

    protected $hidden = [
        'password'
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function adminlte_desc()
    {
        return $this->esAdministrador() ? $this->name : 'Organizador';
    }

    public function esAdministrador(): bool
    {
        // Elige UNA según tu estructura:

        // a) Columna "rol" en la tabla users
        return $this->rol === 'administrador';

        // b) Spatie Laravel Permission
        // return $this->hasRole('administrador');

        // c) Relación con modelo Rol
        // return $this->rol?->nombre === 'administrador';

        // d) Usando el Gate que ya usas en el menú
        // return $this->can('administrar-usuarios');
    }
}
