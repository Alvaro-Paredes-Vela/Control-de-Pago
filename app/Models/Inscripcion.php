<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inscripcion extends Model
{
    protected $table = 'inscripciones';

    protected $fillable = [
        'estudiante_id',
        'actividad_id',
        'costo_acordado',
        'estado',
        'observaciones'
    ];

    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(
            Estudiante::class,
            'estudiante_id'
        );
    }

    public function actividad(): BelongsTo
    {
        return $this->belongsTo(
            Actividad::class,
            'actividad_id'
        );
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(
            Pago::class,
            'inscripcion_id'
        );
    }

    public function respuestas()
    {
        return $this->hasMany(
            RespuestaInscripcion::class,
            'inscripcion_id'
        );
    }
}
