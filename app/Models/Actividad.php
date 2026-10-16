<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Actividad extends Model
{
    protected $table = 'actividades';

    protected $fillable = [
        'categoria_id',
        'creado_por',
        'nombre',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'lugar',
        'costo',
        'cupo_maximo',
        'estado'
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(
            Categoria::class,
            'categoria_id'
        );
    }

    public function inscripciones(): HasMany
    {
        return $this->hasMany(
            Inscripcion::class,
            'actividad_id'
        );
    }

    public function movimientosCaja(): HasMany
    {
        return $this->hasMany(
            MovimientoCaja::class,
            'actividad_id'
        );
    }

    public function campos()
    {
        return $this->hasMany(
            CampoActividad::class,
            'actividad_id'
        )->orderBy('orden')->orderBy('id');
    }
}
