<?php

namespace App\Http\Requests\Admin;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UpdateUsuarioSistemaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('manage-system-users');
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'username' => strtolower(trim((string) $this->input('username'))),
            'email' => $this->filled('email') ? strtolower(trim((string) $this->input('email'))) : null,
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    public function rules(): array
    {
        $user = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:150'],
            'username' => ['required', 'string', 'max:60', 'alpha_dash:ascii', Rule::unique('users', 'username')->ignore($user?->id)],
            'email' => ['nullable', 'email', 'max:191', Rule::unique('users', 'email')->ignore($user?->id)],
            'role_id' => ['required', 'integer', Rule::exists('roles', 'id')],
            'is_active' => ['required', 'boolean'],
            'current_password' => ['nullable', 'string'],
        ];
    }

    public function after(): array
    {
        return [function ($validator) {
            $role = Role::query()->find($this->integer('role_id'));

            if (! $role || ! in_array($role->slug, ['dueno', 'gerencia'], true)) {
                return;
            }

            $currentPassword = (string) $this->input('current_password');

            if ($currentPassword === '') {
                $validator->errors()->add('current_password', 'Confirma tu contraseña actual para asignar o mantener un rol de alta jerarquía.');
                return;
            }

            if (! Hash::check($currentPassword, (string) $this->user()?->password)) {
                $validator->errors()->add('current_password', 'La contraseña actual no es válida.');
            }
        }];
    }
}
