<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUsuarioSistemaRequest;
use App\Http\Requests\Admin\UpdateUsuarioPasswordRequest;
use App\Http\Requests\Admin\UpdateUsuarioSistemaRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UsuarioSistemaController extends Controller
{
    public function index(Request $request): View
    {
        $buscar = trim((string) $request->string('buscar'));
        $roleId = $request->filled('role_id') ? (int) $request->input('role_id') : null;
        $estado = $request->input('estado');
        $estado = in_array($estado, ['1', '0'], true) ? $estado : null;

        $query = User::query()
            ->with('role')
            ->when($buscar !== '', function ($builder) use ($buscar) {
                $builder->where(function ($inner) use ($buscar) {
                    $inner->where('name', 'like', "%{$buscar}%")
                        ->orWhere('username', 'like', "%{$buscar}%")
                        ->orWhere('email', 'like', "%{$buscar}%");
                });
            })
            ->when($roleId, fn ($builder) => $builder->where('role_id', $roleId))
            ->when($estado !== null, fn ($builder) => $builder->where('is_active', (bool) $estado));

        $usuarios = (clone $query)
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        $resumen = [
            'total' => User::query()->count(),
            'activos' => User::query()->where('is_active', true)->count(),
            'inactivos' => User::query()->where('is_active', false)->count(),
            'duenos' => User::query()->whereHas('role', fn ($builder) => $builder->where('slug', 'dueno'))->count(),
            'gerencia' => User::query()->whereHas('role', fn ($builder) => $builder->where('slug', 'gerencia'))->count(),
        ];

        return view('admin.usuarios.index', [
            'usuarios' => $usuarios,
            'buscar' => $buscar,
            'roleId' => $roleId,
            'estado' => $estado,
            'roles' => Role::query()->orderBy('id')->get(),
            'resumen' => $resumen,
        ]);
    }

    public function create(): View
    {
        return view('admin.usuarios.create', [
            'usuario' => new User(['is_active' => true]),
            'roles' => Role::query()->orderBy('id')->get(),
        ]);
    }

    public function store(StoreUsuarioSistemaRequest $request): RedirectResponse
    {
        $data = $request->validated();

        User::query()->create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'] ?? null,
            'password' => $data['password'],
            'role_id' => (int) $data['role_id'],
            'is_active' => (bool) $data['is_active'],
            'paginas_permitidas' => null,
        ]);

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $user): View
    {
        $user->loadMissing('role');

        return view('admin.usuarios.edit', [
            'usuario' => $user,
            'roles' => Role::query()->orderBy('id')->get(),
        ]);
    }

    public function update(UpdateUsuarioSistemaRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        $this->ensureOwnerIntegrity($user, (int) $data['role_id'], (bool) $data['is_active']);

        $user->update([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'] ?? null,
            'role_id' => (int) $data['role_id'],
            'is_active' => (bool) $data['is_active'],
        ]);

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function editPassword(User $user): View
    {
        $user->loadMissing('role');

        return view('admin.usuarios.password', [
            'usuario' => $user,
        ]);
    }

    public function updatePassword(UpdateUsuarioPasswordRequest $request, User $user): RedirectResponse
    {
        $user->update([
            'password' => $request->validated('password'),
        ]);

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', 'Contraseña actualizada correctamente.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ((int) $request->user()->id === (int) $user->id) {
            throw ValidationException::withMessages([
                'user' => 'No puedes eliminar tu propia cuenta autenticada.',
            ]);
        }

        $this->ensureOwnerIntegrity($user, null, null, true);
        $user->delete();

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }

    protected function ensureOwnerIntegrity(User $user, ?int $newRoleId = null, ?bool $newIsActive = null, bool $deleting = false): void
    {
        $user->loadMissing('role');

        if (! $user->hasRole('dueno')) {
            return;
        }

        $ownerRoleId = Role::query()->where('slug', 'dueno')->value('id');
        $activeOwnerCount = User::query()
            ->where('role_id', $ownerRoleId)
            ->where('is_active', true)
            ->count();

        $willRemainOwner = $newRoleId === null ? true : ((int) $newRoleId === (int) $ownerRoleId);
        $willRemainActive = $newIsActive === null ? (bool) $user->is_active : $newIsActive;

        if (! $deleting && $willRemainOwner && $willRemainActive) {
            return;
        }

        if ($activeOwnerCount <= 1) {
            throw ValidationException::withMessages([
                'role_id' => 'No puedes dejar el sistema sin al menos un usuario dueño activo.',
            ]);
        }
    }
}
