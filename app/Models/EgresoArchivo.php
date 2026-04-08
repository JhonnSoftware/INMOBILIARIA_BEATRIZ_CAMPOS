<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EgresoArchivo extends Model
{
    protected $table = 'egreso_archivos';

    protected $fillable = [
        'egreso_id',
        'nombre_archivo',
        'nombre_original',
        'ruta_archivo',
        'tipo_archivo',
        'tamano_archivo',
    ];

    protected $casts = [
        'tamano_archivo' => 'integer',
    ];

    public function egreso(): BelongsTo
    {
        return $this->belongsTo(Egreso::class, 'egreso_id');
    }
}
