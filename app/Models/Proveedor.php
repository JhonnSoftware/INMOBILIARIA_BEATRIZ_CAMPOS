<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Proveedor extends Model
{
    protected $table = 'proveedores';

    protected $fillable = [
        'empresa',
        'ruc',
        'persona_contacto',
        'telefono',
        'departamento',
        'provincia',
        'distrito',
        'email',
        'categoria',
        'subcategoria',
        'descripcion_servicio',
        'yape_plin',
        'cuenta_bancaria',
        'proximo_pago',
        'monto_total',
        'monto_pagado',
        'contrato_path',
        'contrato_original_name',
    ];

    protected $casts = [
        'proximo_pago' => 'date',
        'monto_total' => 'decimal:2',
        'monto_pagado' => 'decimal:2',
    ];

    public function egresos(): HasMany
    {
        return $this->hasMany(Egreso::class, 'proveedor_id');
    }

    public function getMontoPendienteAttribute(): float
    {
        return max((float) $this->monto_total - (float) $this->monto_pagado, 0);
    }

    public function getContratoUrlAttribute(): ?string
    {
        if (! $this->contrato_path) {
            return null;
        }

        return Storage::disk('public')->url($this->contrato_path);
    }

    public function getUbicacionCompletaAttribute(): string
    {
        return collect([$this->departamento, $this->provincia, $this->distrito])
            ->filter(fn ($value) => filled($value))
            ->implode(', ');
    }
}
