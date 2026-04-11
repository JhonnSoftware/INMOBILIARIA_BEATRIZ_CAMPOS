<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequerimientoCajaChica extends Model
{
    protected $table = 'requerimientos_caja_chica';

    protected $fillable = [
        'user_id',
        'fecha_solicitud',
        'monto',
        'proyecto',
        'detalle',
        'archivo_path',
        'archivo_nombre',
        'estado',
        'observacion_admin',
        'revisado_por',
        'revisado_at',
    ];

    protected $casts = [
        'fecha_solicitud' => 'date',
        'monto'           => 'decimal:2',
        'revisado_at'     => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function revisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revisado_por');
    }

    public function getEstadoBadgeAttribute(): array
    {
        return match($this->estado) {
            'aprobado'  => ['label' => 'Aprobado',  'color' => '#16a34a', 'bg' => '#dcfce7', 'icon' => 'fa-check'],
            'rechazado' => ['label' => 'Rechazado', 'color' => '#dc2626', 'bg' => '#fee2e2', 'icon' => 'fa-times'],
            default     => ['label' => 'Pendiente', 'color' => '#d97706', 'bg' => '#fef3c7', 'icon' => 'fa-clock'],
        };
    }
}
