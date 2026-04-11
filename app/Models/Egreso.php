<?php

namespace App\Models;

use App\Support\EgresoCatalog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Egreso extends Model
{
    protected $table = 'egresos';

    protected $fillable = [
        'proyecto_id',
        'fecha',
        'categoria_principal',
        'categoria',
        'monto',
        'descripcion',
        'observaciones',
        'responsable',
        'proveedor_id',
        'fuente_dinero',
        'tipo_comprobante',
        'serie_comprobante',
        'numero_comprobante',
        'ruc_proveedor',
        'razon_social',
        'tipo_compra',
        'detalles_proveedor',
        'estado',
        'creado_por',
        'updated_by',
    ];

    protected $casts = [
        'fecha' => 'date',
        'monto' => 'decimal:2',
        'proveedor_id' => 'integer',
    ];

    public function proyecto(): BelongsTo
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id');
    }

    public function archivos(): HasMany
    {
        return $this->hasMany(EgresoArchivo::class, 'egreso_id')->orderByDesc('created_at');
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    public static function fuentesDinero(): array
    {
        return EgresoCatalog::FUENTES_DINERO;
    }

    public static function estados(): array
    {
        return EgresoCatalog::ESTADOS;
    }
}
