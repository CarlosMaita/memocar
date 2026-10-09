<?php

namespace App\Http\Middleware;

use App\Models\Car;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Todo el dashboard, registros y catálogo trabajan sobre el carro seleccionado en la sesión.
 * Sin carro válido → selector (si el usuario tiene uno solo, se selecciona automáticamente).
 */
class EnsureCarSelected
{
    public const SESSION_KEY = 'current_car_id';

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $car = $user->cars()->find($request->session()->get(self::SESSION_KEY));

        if (! $car && $user->cars()->count() === 1) {
            $car = $user->cars()->first();
            $request->session()->put(self::SESSION_KEY, $car->id);
        }

        if (! $car) {
            return redirect()->route('cars.select');
        }

        $request->attributes->set('car', $car);
        View::share('currentCar', $car);

        return $next($request);
    }

    public static function select(Request $request, Car $car): void
    {
        $request->session()->put(self::SESSION_KEY, $car->id);
    }
}
