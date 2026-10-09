<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRecord;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MaintenanceRecordController extends Controller
{
    public function index(Request $request): View
    {
        $car = $this->car();
        $filters = $request->validate([
            'type' => ['nullable', 'integer'],
            'year' => ['nullable', 'integer'],
        ]);

        $query = $car->records()->with('type')
            ->when($filters['type'] ?? null, fn ($q, $type) => $q->where('maintenance_type_id', $type))
            ->when($filters['year'] ?? null, fn ($q, $year) => $q->whereYear('date', $year));

        $records = $query->clone()->orderByDesc('date')->orderByDesc('id')->paginate(30)->withQueryString();

        return view('records.index', [
            'records' => $records,
            'recordsByYear' => $records->getCollection()->groupBy(fn (MaintenanceRecord $r) => $r->date->year),
            'yearTotals' => $query->clone()->get(['date', 'cost'])->groupBy(fn (MaintenanceRecord $r) => $r->date->year)->map->sum('cost'),
            'total' => (float) $query->clone()->sum('cost'),
            'count' => $records->total(),
            'types' => $request->user()->maintenanceTypes()->whereIn('id', $car->records()->select('maintenance_type_id'))->orderBy('name')->get(),
            'years' => $car->records()->pluck('date')->map(fn ($date) => Carbon::parse($date)->year)->unique()->sortDesc()->values(),
            'filters' => $filters,
        ]);
    }

    public function create(Request $request): View
    {
        $record = new MaintenanceRecord([
            'date' => now()->toDateString(),
            'km' => $this->car()->current_km ?: null,
            'maintenance_type_id' => $request->integer('type') ?: null,
        ]);

        return $this->form($request, $record);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->car()->records()->create($this->validated($request));

        return redirect()->route('records.index')->with('status', 'Registro guardado.');
    }

    public function edit(Request $request, MaintenanceRecord $record): View
    {
        $this->authorizeRecord($record);

        return $this->form($request, $record);
    }

    public function update(Request $request, MaintenanceRecord $record): RedirectResponse
    {
        $this->authorizeRecord($record);
        $record->update($this->validated($request));

        return redirect()->route('records.index')->with('status', 'Registro actualizado.');
    }

    public function destroy(MaintenanceRecord $record): RedirectResponse
    {
        $this->authorizeRecord($record);
        $record->delete();

        return redirect()->route('records.index')->with('status', 'Registro eliminado.');
    }

    private function form(Request $request, MaintenanceRecord $record): View
    {
        $types = $request->user()->maintenanceTypes()
            ->where(fn ($q) => $q->where('is_active', true)->orWhere('id', $record->maintenance_type_id))
            ->orderBy('category')->orderBy('name')->get();

        return view('records.form', ['record' => $record, 'typesByCategory' => $types->groupBy('category')]);
    }

    private function authorizeRecord(MaintenanceRecord $record): void
    {
        abort_unless($record->car_id === $this->car()->id, 404);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'date' => ['required', 'date', 'before_or_equal:today'],
            'maintenance_type_id' => ['required', Rule::exists('maintenance_types', 'id')->where('user_id', $request->user()->id)],
            'km' => ['nullable', 'integer', 'min:0', 'max:9999999'],
            'description' => ['nullable', 'string', 'max:2000'],
            'cost' => ['required', 'numeric', 'min:0', 'max:99999999'],
        ], [
            'date.before_or_equal' => 'La fecha no puede ser futura.',
        ], [
            'maintenance_type_id' => 'concepto',
            'date' => 'fecha',
            'cost' => 'costo',
        ]);
    }
}
