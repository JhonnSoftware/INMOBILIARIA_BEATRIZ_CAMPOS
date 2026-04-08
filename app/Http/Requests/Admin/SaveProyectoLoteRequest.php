<?php

namespace App\Http\Requests\Admin;

use App\Models\Lote;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveProyectoLoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $proyecto = $this->route('proyecto');
        $lote = $this->route('lote');

        return [
            'manzana' => ['required', 'string', 'max:20'],
            'numero' => [
                'required',
                'string',
                'max:20',
                Rule::unique('lotes', 'numero')
                    ->where(fn ($query) => $query
                        ->where('proyecto_id', $proyecto->id)
                        ->where('manzana', $this->input('manzana'))
                    )
                    ->ignore($lote?->id),
            ],
            'codigo' => ['nullable', 'string', 'max:50'],
            'metraje' => ['required', 'numeric', 'min:0'],
            'precio_inicial' => ['required', 'numeric', 'min:0'],
            'estado' => ['required', Rule::in(Lote::ESTADOS)],
            'descripcion' => ['nullable', 'string'],
            'observaciones' => ['nullable', 'string'],
            'fecha_venta' => ['nullable', 'date'],
        ];
    }

    public function attributes(): array
    {
        return [
            'numero' => 'numero de lote',
            'precio_inicial' => 'precio inicial',
            'fecha_venta' => 'fecha de venta',
        ];
    }
}
