<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Maneja la petición entrante.
     * El parámetro ...$roles recibe los roles permitidos desde la ruta.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // 1. Verificamos si el usuario está autenticado
        // 2. Comparamos el VALOR del Enum del usuario contra la lista de roles permitidos
        if (!auth()->check() || !in_array($request->user()->role->value, $roles)) {
            // Si no tiene permiso, lanzamos un error 403 (Prohibido)
            abort(403, 'No tienes permisos jerárquicos para acceder a esta sección.');
        }

        return $next($request);
    }
}