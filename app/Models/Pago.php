<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends Model
{
    protected $table = 'pagos';

    protected $fillable = [
        'inscripcion_id',
        'metodo_pago_id',
        'recibido_por',
        'monto',
        'fecha_pago',
        'referencia',
        'observaciones',
        'estado'
    ];

    public function inscripcion(): BelongsTo
    {
        return $this->belongsTo(
            Inscripcion::class,
            'inscripcion_id'
        );
    }

    public function metodoPago(): BelongsTo
    {
        return $this->belongsTo(
            MetodoPago::class,
            'metodo_pago_id'
        );
    }

    public function receptor(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'recibido_por'
        );
    }
}
