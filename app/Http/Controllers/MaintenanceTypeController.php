<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** Catálogo de fallas/mantenimientos del usuario (común a todos sus carros). */
class MaintenanceTypeController extends Controller
{
    public function index(Request $request): View
    {
        $types = $request->user()->maintenanceTypes()
            ->withCount(['records' => fn ($q) => $q->where('car_id', $this->car()->id)])
            ->orderBy('category')->orderBy('name')->get();

        return view('maintenance-types.index', ['typesByCategory' => $types->groupBy('category')]);
    }

    public function create(): View
    {
        return view('maintenance-types.form', ['type' => new MaintenanceType(['is_active' => true]), 'categories' => $this->categories()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->user()->maintenanceTypes()->create($this->validated($request));

        return redirect()->route('maintenance-types.index')->with('status', 'Concepto agregado al catálogo.');
    }

    public function edit(Request $request, MaintenanceType $maintenanceType): View
    {
        $this->authorizeType($request, $maintenanceType);

        return view('maintenance-types.form', ['type' => $maintenanceType, 'categories' => $this->categories()]);
    }

    public function update(Request $request, MaintenanceType $maintenanceType): RedirectResponse
    {
        $this->authorizeType($request, $maintenanceType);
        $maintenanceType->update($this->validated($request, $maintenanceType));

        return redirect()->route('maintenance-types.index')->with('status', 'Concepto actualizado.');
    }

    /** Restablece el intervalo seteado al recomendado. */
    public function reset(Request $request, MaintenanceType $maintenanceType): RedirectResponse
    {
        $this->authorizeType($request, $maintenanceType);
        $maintenanceType->update([
            'set_km' => $maintenanceType->recommended_km,
            'set_months' => $maintenanceType->recommended_months,
        ]);

        return back()->with('status', 'Intervalo restablecido al recomendado.');
    }

    public function destroy(Request $request, MaintenanceType $maintenanceType): RedirectResponse
    {
        $this->authorizeType($request, $maintenanceType);

        if ($maintenanceType->records()->exists()) {
            return back()->with('error', 'No se puede eliminar: tiene registros. Puedes desactivarlo.');
        }
        $maintenanceType->delete();

        return redirect()->route('maintenance-types.index')->with('status', 'Concepto eliminado.');
    }

    private function authorizeType(Request $request, MaintenanceType $type): void
    {
        abort_unless($type->user_id === $request->user()->id, 404);
    }

    private function categories(): array
    {
        return request()->user()->maintenanceTypes()->distinct()->orderBy('category')->pluck('category')->all();
    }

    private function validated(Request $request, ?MaintenanceType $type = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120', Rule::unique('maintenance_types')->where('user_id', $request->user()->id)->ignore($type?->id)],
            'category' => ['required', 'string', 'max:60'],
            'set_km' => ['nullable', 'integer', 'min:1', 'max:9999999'],
            'set_months' => ['nullable', 'integer', 'min:1', 'max:600'],
            'notes' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ], [], ['set_km' => 'km', 'set_months' => 'meses', 'name' => 'nombre', 'category' => 'categoría']);

        return $data + ['is_active' => false];
    }
}
