<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'login' => ['required', 'string', 'max:191'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ], [], [
            'login' => 'usuario o correo',
            'password' => 'contraseña',
        ]);

        $login = trim((string) $credentials['login']);
        $user = User::query()
            ->with('role')
            ->where('username', $login)
            ->orWhere('email', $login)
            ->first();

        if (! $user || ! Hash::check((string) $credentials['password'], (string) $user->password)) {
            throw ValidationException::withMessages([
                'login' => 'Las credenciales proporcionadas no son válidas.',
            ]);
        }

        if (! $user->role_id) {
            throw ValidationException::withMessages([
                'login' => 'Tu cuenta no tiene un rol asignado. Contacta al dueño del sistema.',
            ]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'login' => 'Tu cuenta está inactiva. Contacta al dueño del sistema.',
            ]);
        }

        Auth::login($user, (bool) ($credentials['remember'] ?? false));
        $request->session()->regenerate();
        $user->forceFill(['last_login_at' => now()])->save();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Sesión cerrada correctamente.');
    }
}