@push('styles')
<style>
    .helper-text{margin-top:8px;font-size:12px;color:var(--gray);line-height:1.6;}
    .role-warning{padding:14px 16px;border-radius:16px;background:#fff7ed;border:1px solid #fed7aa;color:#9a3412;font-size:12px;line-height:1.6;display:none;}
    .role-warning.show{display:block;}
    .switch-row{display:flex;align-items:center;gap:10px;padding:14px 16px;border-radius:16px;background:var(--bg);border:1px solid var(--border);}
    .switch-row input{width:18px;height:18px;accent-color:var(--mg);}
</style>
@endpush

<div class="form-grid">
    <div class="form-group">
        <label for="name">Nombre completo <span class="req">*</span></label>
        <input type="text" id="name" name="name" value="{{ old('name', $usuario->name) }}" maxlength="150" required>
        @error('name')<div class="error-text">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
        <label for="username">Username <span class="req">*</span></label>
        <input type="text" id="username" name="username" value="{{ old('username', $usuario->username) }}" maxlength="60" required>
        <div class="helper-text">Solo letras, números, guiones y guion bajo.</div>
        @error('username')<div class="error-text">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
        <label for="email">Correo electrónico</label>
        <input type="email" id="email" name="email" value="{{ old('email', $usuario->email) }}" maxlength="191">
        @error('email')<div class="error-text">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
        <label for="role_id">Rol <span class="req">*</span></label>
        <select id="role_id" name="role_id" required>
            <option value="">Selecciona un rol</option>
            @foreach($roles as $role)
            <option value="{{ $role->id }}" data-slug="{{ $role->slug }}" @selected((string) old('role_id', $usuario->role_id) === (string) $role->id)>{{ $role->nombre }}</option>
            @endforeach
        </select>
        @error('role_id')<div class="error-text">{{ $message }}</div>@enderror
    </div>

    @if(($showPasswordFields ?? false) === true)
    <div class="form-group">
        <label for="password">Contraseña <span class="req">*</span></label>
        <input type="password" id="password" name="password" required>
        @error('password')<div class="error-text">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
        <label for="password_confirmation">Confirmar contraseña <span class="req">*</span></label>
        <input type="password" id="password_confirmation" name="password_confirmation" required>
    </div>
    @endif

    <div class="form-group full">
        <div class="switch-row">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $usuario->is_active ?? true))>
            <label for="is_active" style="margin:0;font-size:13px;font-weight:700;letter-spacing:0;text-transform:none;cursor:pointer;">Usuario activo y habilitado para iniciar sesión</label>
        </div>
        @error('is_active')<div class="error-text">{{ $message }}</div>@enderror
    </div>

    <div class="form-group full role-security-wrap">
        <label for="current_password">Confirmar tu contraseña actual</label>
        <input type="password" id="current_password" name="current_password">
        <div class="helper-text">Se solicitará cuando asignes o mantengas roles de alta jerarquía como <strong>dueño</strong> o <strong>gerencia</strong>.</div>
        <div class="role-warning" id="roleWarning">Estás gestionando un usuario de alta jerarquía. Para continuar, confirma tu contraseña actual como verificación adicional.</div>
        @error('current_password')<div class="error-text">{{ $message }}</div>@enderror
    </div>
</div>

<div class="form-actions">
    <a href="{{ route('admin.usuarios.index') }}" class="btn-secondary">Cancelar</a>
    <button type="submit" class="btn-primary"><i class="fas fa-save"></i> {{ $submitLabel }}</button>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const role = document.getElementById('role_id');
        const currentPassword = document.getElementById('current_password');
        const warning = document.getElementById('roleWarning');

        if (!role || !currentPassword || !warning) {
            return;
        }

        const syncRoleSecurity = () => {
            const selected = role.options[role.selectedIndex];
            const slug = selected?.dataset?.slug || '';
            const requires = ['dueno', 'gerencia'].includes(slug);

            currentPassword.required = requires;
            warning.classList.toggle('show', requires);
        };

        role.addEventListener('change', syncRoleSecurity);
        syncRoleSecurity();
    });
</script>
@endpush
