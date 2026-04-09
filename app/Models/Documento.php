<?php

namespace App\Models;

use App\Support\DocumentoCatalog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Documento extends Model
{
    public const CONTEXTOS = [
        'proyecto',
        'lote',
        'cliente',
        'operacion',
    ];

    public const TIPOS = [
        'contrato',
        'voucher',
        'reserva',
        'financiamiento',
        'venta',
        'plano',
        'dni',
        'ficha_cliente',
        'anexo',
        'otro',
    ];

    public const ESTADOS = [
        'activo',
        'eliminado',
    ];

    protected $table = 'documentos';

    protected $fillable = [
        'proyecto_id',
        'lote_id',
        'cliente_id',
        'pago_id',
        'contexto',
        'tipo_documento',
        'titulo',
        'descripcion',
        'nombre_original',
        'nombre_archivo',
        'ruta_archivo',
        'extension',
        'mime_type',
        'tamano_archivo',
        'estado',
        'fecha_documento',
        'subido_por',
    ];

    protected $casts = [
        'fecha_documento' => 'date',
        'tamano_archivo' => 'integer',
    ];

    public function proyecto(): BelongsTo
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id');
    }

    public function lote(): BelongsTo
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function pago(): BelongsTo
    {
        return $this->belongsTo(Pago::class, 'pago_id');
    }

    public function getNombreAttribute(): string
    {
        return (string) ($this->titulo ?: $this->nombre_original ?: 'Documento');
    }

    public function getRutaAttribute(): ?string
    {
        return $this->ruta_archivo;
    }

    public function getTipoAttribute(): ?string
    {
        return $this->mime_type;
    }

    public function getTamanioAttribute(): ?int
    {
        return $this->tamano_archivo;
    }

    public function getTamanoFormateadoAttribute(): string
    {
        return DocumentoCatalog::humanSize($this->tamano_archivo);
    }
}
