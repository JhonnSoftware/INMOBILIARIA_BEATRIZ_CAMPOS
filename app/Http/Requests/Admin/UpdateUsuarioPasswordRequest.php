<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUsuarioPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('manage-system-users');
    }

    public function rules(): array
    {
        return [
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
