<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Lote extends Model
{
    protected $table = 'lotes';

    protected $fillable = [
        'proyecto_id',
        'manzana',
        'numero',
        'metraje',
        'precio_inicial',
        'estado',
        'notas',
    ];

    protected $casts = [
        'metraje'       => 'decimal:2',
        'precio_inicial' => 'decimal:2',
    ];

    public function proyecto(): BelongsTo
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id');
    }

    public function contrato(): HasOne
    {
        return $this->hasOne(Contrato::class, 'lote_id');
    }
}
