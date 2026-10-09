<?php

namespace App\Http\Controllers;

use App\Http\Middleware\EnsureCarSelected;
use App\Models\Car;
use App\Services\MaintenanceSchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CarController extends Controller
{
    public function index(Request $request): View
    {
        return view('cars.index', [
            'cars' => $request->user()->cars()->withCount('records')->withSum('records', 'cost')->orderBy('name')->get(),
            'selectedId' => $request->session()->get(EnsureCarSelected::SESSION_KEY),
        ]);
    }

    /** Pantalla de entrada: elegir el carro a revisar. */
    public function select(Request $request, MaintenanceSchedule $schedule): View|RedirectResponse
    {
        $cars = $request->user()->cars()->withCount('records')->orderBy('name')->get();

        if ($cars->isEmpty()) {
            return redirect()->route('cars.create');
        }

        return view('cars.select', [
            'cars' => $cars,
            'overdue' => $cars->mapWithKeys(fn (Car $car) => [
                $car->id => $schedule->forCar($car)->where('status', MaintenanceSchedule::OVERDUE)->count(),
            ]),
            'selectedId' => $request->session()->get(EnsureCarSelected::SESSION_KEY),
        ]);
    }

    public function choose(Request $request, Car $car): RedirectResponse
    {
        $this->authorizeCar($request, $car);
        EnsureCarSelected::select($request, $car);

        return redirect()->route('dashboard');
    }

    public function create(Request $request): View
    {
        return view('cars.form', ['car' => new Car, 'first' => ! $request->user()->cars()->exists()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $car = $request->user()->cars()->create($this->validated($request));
        EnsureCarSelected::select($request, $car);

        return redirect()->route('dashboard')->with('status', "Carro «{$car->name}» creado.");
    }

    public function edit(Request $request, Car $car): View
    {
        $this->authorizeCar($request, $car);

        return view('cars.form', ['car' => $car, 'first' => false]);
    }

    public function update(Request $request, Car $car): RedirectResponse
    {
        $this->authorizeCar($request, $car);
        $car->update($this->validated($request));

        return redirect()->route('cars.index')->with('status', 'Carro actualizado.');
    }

    /** Actualización rápida del kilometraje desde el dashboard. */
    public function updateKm(Request $request): RedirectResponse
    {
        $data = $request->validate(['current_km' => ['required', 'integer', 'min:0', 'max:9999999']]);
        $this->car()->update($data);

        return back()->with('status', 'Kilometraje actualizado.');
    }

    public function destroy(Request $request, Car $car): RedirectResponse
    {
        $this->authorizeCar($request, $car);
        $car->delete();

        if ($request->session()->get(EnsureCarSelected::SESSION_KEY) === $car->id) {
            $request->session()->forget(EnsureCarSelected::SESSION_KEY);
        }

        return redirect()->route('cars.index')->with('status', 'Carro eliminado junto con su historial.');
    }

    private function authorizeCar(Request $request, Car $car): void
    {
        abort_unless($car->user_id === $request->user()->id, 404);
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:60'],
            'brand' => ['nullable', 'string', 'max:60'],
            'model' => ['nullable', 'string', 'max:60'],
            'year' => ['nullable', 'integer', 'min:1900', 'max:'.(date('Y') + 1)],
            'plate' => ['nullable', 'string', 'max:20'],
            'current_km' => ['nullable', 'integer', 'min:0', 'max:9999999'],
        ]);
        $data['current_km'] ??= 0;

        return $data;
    }
}
