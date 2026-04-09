<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        abort_unless($user, 403, 'Debes iniciar sesión para acceder a este módulo.');
        abort_unless($user->hasRole($roles), 403, 'No tienes permiso para acceder a este módulo.');

        return $next($request);
    }
}
