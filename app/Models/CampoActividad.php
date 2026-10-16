<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CampoActividad extends Model
{
    protected $table = 'campos_actividad';

    protected $fillable = [
        'actividad_id',
        'nombre',
        'etiqueta',
        'tipo',
        'opciones',
        'obligatorio',
        'orden'
    ];

    protected function casts(): array
    {
        return [
            'opciones' => 'array',
            'obligatorio' => 'boolean',
        ];
    }

    public function actividad(): BelongsTo
    {
        return $this->belongsTo(
            Actividad::class,
            'actividad_id'
        );
    }

    public function respuestas(): HasMany
    {
        return $this->hasMany(
            RespuestaInscripcion::class,
            'campo_id'
        );
    }
}
