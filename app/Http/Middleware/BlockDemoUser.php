<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Las cuentas demo no pueden cambiar correo/contraseña ni borrar la cuenta. */
class BlockDemoUser
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->is_demo) {
            return redirect()->route('dashboard')->with('error', 'Esta opción no está disponible en el modo demo. Crea tu cuenta gratis para usarla.');
        }

        return $next($request);
    }
}
