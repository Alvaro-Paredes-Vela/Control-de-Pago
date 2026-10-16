<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Estudiante extends Model
{
    protected $table = 'estudiantes';

    protected $fillable = [
        'registro_universitario',
        'nombres',
        'apellidos',
        'celular',
        'correo',
        'semestre',
        'estado'
    ];

    public function inscripciones(): HasMany
    {
        return $this->hasMany(
            Inscripcion::class,
            'estudiante_id'
        );
    }
}
