<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Colaborador extends Model
{
    protected $table = 'colaboradores';

    protected $fillable = [
        'nombre',
        'apellido',
        'cargo',
        'celular',
        'dni',
        'redes_sociales',
        'departamento',
        'subdepartamento',
        'area',
        'honorarios',
        'fecha_pago',
        'tipo_pago',
        'foto_path',
        'foto_original_name',
        'contrato_path',
        'contrato_original_name',
    ];

    protected $casts = [
        'honorarios' => 'decimal:2',
    ];

    public function getNombreCompletoAttribute(): string
    {
        return trim($this->nombre . ' ' . $this->apellido);
    }

    public function getInicialesAttribute(): string
    {
        return collect([$this->nombre, $this->apellido])
            ->filter()
            ->map(fn ($part) => strtoupper(substr((string) $part, 0, 1)))
            ->implode('');
    }

    public function getFotoUrlAttribute(): ?string
    {
        if (! $this->foto_path) {
            return null;
        }

        return Storage::disk('public')->url($this->foto_path);
    }

    public function getContratoUrlAttribute(): ?string
    {
        if (! $this->contrato_path) {
            return null;
        }

        return Storage::disk('public')->url($this->contrato_path);
    }
}
