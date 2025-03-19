<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; // ✅ Vérifie cet import
use App\Models\Role; // ✅ Assure-toi que cet import est bien présent

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role): Response
    {
        if (!Auth::check()) {
            abort(403, 'Access Denied');
        }

        $user = Auth::user();

        // Vérifier si l'utilisateur a un rôle et que celui-ci correspond au rôle attendu
        if (!$user->role || $user->role->name !== $role) {
            abort(403, 'Access Denied');
        }

        return $next($request);
    }
}
