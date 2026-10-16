<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RespuestaInscripcion extends Model
{
    protected $table = 'respuestas_inscripcion';

    protected $fillable = [
        'inscripcion_id',
        'campo_id',
        'valor'
    ];

    public function inscripcion(): BelongsTo
    {
        return $this->belongsTo(
            Inscripcion::class,
            'inscripcion_id'
        );
    }

    public function campo(): BelongsTo
    {
        return $this->belongsTo(
            CampoActividad::class,
            'campo_id'
        );
    }
}
