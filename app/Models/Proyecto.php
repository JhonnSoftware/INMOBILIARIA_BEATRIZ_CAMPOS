<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Proyecto extends Model
{
    protected $table = 'proyectos';

    protected $fillable = [
        'codigo',
        'nombre',
        'slug',
        'nombre_corto',
        'ubicacion',
        'direccion',
        'referencia',
        'distrito',
        'provincia',
        'departamento',
        'ubigeo',
        'descripcion',
        'precio_base',
        'imagen',
        'logo',
        'imagen_portada',
        'icono_menu',
        'latitud',
        'longitud',
        'url_ubicacion_maps',
        'url_3d',
        'plano_general',
        'orden_menu',
        'fecha_inicio',
        'fecha_lanzamiento',
        'observaciones',
        'estado',
    ];

    protected $casts = [
        'precio_base' => 'decimal:2',
        'latitud' => 'decimal:7',
        'longitud' => 'decimal:7',
        'orden_menu' => 'integer',
        'fecha_inicio' => 'date',
        'fecha_lanzamiento' => 'date',
    ];

    protected static function booted(): void
    {
        static::saving(function (Proyecto $proyecto) {
            if (blank($proyecto->codigo)) {
                $proyecto->codigo = static::generateNextCodigo($proyecto->getKey());
            }

            if (blank($proyecto->slug) || $proyecto->isDirty('nombre')) {
                $proyecto->slug = static::generateUniqueSlug($proyecto->slug ?: $proyecto->nombre, $proyecto->getKey());
            }

            if (blank($proyecto->direccion) && filled($proyecto->ubicacion)) {
                $proyecto->direccion = $proyecto->ubicacion;
            }

            if (blank($proyecto->logo) && filled($proyecto->imagen)) {
                $proyecto->logo = $proyecto->imagen;
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function lotes(): HasMany
    {
        return $this->hasMany(Lote::class, 'proyecto_id');
    }

    public function clientes(): HasMany
    {
        return $this->hasMany(Cliente::class, 'proyecto_id');
    }

    public function contratos(): HasMany
    {
        return $this->hasMany(Contrato::class, 'proyecto_id');
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class, 'proyecto_id');
    }

    public function cronogramaPagos(): HasMany
    {
        return $this->hasMany(CronogramaPago::class, 'proyecto_id');
    }

    public function ingresos(): HasMany
    {
        return $this->hasMany(Ingreso::class, 'proyecto_id');
    }

    public function egresos(): HasMany
    {
        return $this->hasMany(Egreso::class, 'proyecto_id');
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class, 'proyecto_id');
    }

    protected static function generateNextCodigo(?int $ignoreId = null): string
    {
        $correlativo = $ignoreId ?? ((static::max('id') ?? 0) + 1);
        $codigo = 'PRY-' . str_pad((string) $correlativo, 4, '0', STR_PAD_LEFT);

        while (static::query()
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->where('codigo', $codigo)
            ->exists()
        ) {
            $correlativo++;
            $codigo = 'PRY-' . str_pad((string) $correlativo, 4, '0', STR_PAD_LEFT);
        }

        return $codigo;
    }

    protected static function generateUniqueSlug(?string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value ?: 'proyecto');
        $base = $base !== '' ? $base : 'proyecto';
        $slug = $base;
        $suffix = 2;

        while (static::query()
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->where('slug', $slug)
            ->exists()
        ) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
