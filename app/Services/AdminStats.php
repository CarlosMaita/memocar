<?php

namespace App\Services;

use App\Models\Car;
use App\Models\MaintenanceRecord;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Estadísticas generales para el panel de administración.
 * Por defecto excluye las cuentas demo (sus datos son de ejemplo).
 */
class AdminStats
{
    public const MONTHS = 12;

    public function __construct(private bool $includeDemo = false) {}

    public function kpis(): array
    {
        $users = User::where('is_demo', false);
        $records = $this->records();

        return [
            'users' => (clone $users)->count(),
            'usersLast30' => (clone $users)->where('created_at', '>=', now()->subDays(30))->count(),
            'usersWithCars' => (clone $users)->whereHas('cars')->count(),
            'demoActive' => User::where('is_demo', true)->count(),
            'cars' => $this->cars()->count(),
            'carOwners' => $this->cars()->distinct()->count('user_id'),
            'records' => (clone $records)->count(),
            'recordsLast30' => (clone $records)->where('maintenance_records.created_at', '>=', now()->subDays(30))->count(),
            'cost' => (float) (clone $records)->sum('maintenance_records.cost'),
        ];
    }

    /**
     * Espectro de fallas: frecuencia de cada concepto (agrupado por nombre, ya que el
     * catálogo es por usuario), con su participación, acumulado (Pareto), costo promedio
     * y en cuántos carros aparece.
     *
     * @return Collection<int, array{name: string, category: string, count: int, share: float, cumulative: float, avgCost: float, cars: int}>
     */
    public function spectrum(): Collection
    {
        $rows = $this->records()
            ->join('maintenance_types', 'maintenance_types.id', '=', 'maintenance_records.maintenance_type_id')
            ->selectRaw('maintenance_types.name as name, maintenance_types.category as category, count(*) as total, avg(maintenance_records.cost) as avg_cost, count(distinct maintenance_records.car_id) as cars')
            ->groupBy('maintenance_types.name', 'maintenance_types.category')
            ->orderByDesc('total')->orderBy('maintenance_types.name')
            ->toBase()->get();

        $sum = max(1, (int) $rows->sum('total'));
        $running = 0;

        return $rows->map(function ($row) use ($sum, &$running) {
            $running += $row->total;

            return [
                'name' => $row->name,
                'category' => $row->category,
                'count' => (int) $row->total,
                'share' => $row->total / $sum * 100,
                'cumulative' => $running / $sum * 100,
                'avgCost' => (float) $row->avg_cost,
                'cars' => (int) $row->cars,
            ];
        });
    }

    /** Registros por categoría. */
    public function byCategory(): Collection
    {
        return $this->spectrum()->groupBy('category')
            ->map(fn (Collection $items, string $category) => ['category' => $category, 'count' => $items->sum('count')])
            ->sortByDesc('count')->values();
    }

    /**
     * Espectrograma: registros por categoría y mes (últimos 12 meses, por fecha del servicio).
     *
     * @return array{months: list<CarbonImmutable>, rows: Collection, max: int}
     */
    public function spectrogram(): array
    {
        $start = CarbonImmutable::today()->startOfMonth()->subMonths(self::MONTHS - 1);
        $months = collect(range(0, self::MONTHS - 1))->map(fn ($i) => $start->addMonths($i));

        $records = $this->records()
            ->join('maintenance_types', 'maintenance_types.id', '=', 'maintenance_records.maintenance_type_id')
            ->where('maintenance_records.date', '>=', $start->toDateString())
            ->get(['maintenance_records.date', 'maintenance_types.category']);

        $rows = $records->groupBy('category')->map(function (Collection $items, string $category) use ($months) {
            $byMonth = $items->countBy(fn ($r) => $r->date->format('Y-m'));

            return [
                'category' => $category,
                'cells' => $months->map(fn (CarbonImmutable $m) => $byMonth[$m->format('Y-m')] ?? 0)->all(),
                'total' => $items->count(),
            ];
        })->sortByDesc('total')->values();

        return ['months' => $months->all(), 'rows' => $rows, 'max' => max(1, (int) $rows->flatMap(fn ($r) => $r['cells'])->max())];
    }

    /** Registros cargados por mes (fecha de carga), últimos 12 meses. */
    public function recordsPerMonth(): Collection
    {
        $start = CarbonImmutable::today()->startOfMonth()->subMonths(self::MONTHS - 1);
        $counts = $this->records()->where('maintenance_records.created_at', '>=', $start)
            ->get(['maintenance_records.created_at'])
            ->countBy(fn ($r) => $r->created_at->format('Y-m'));

        return collect(range(0, self::MONTHS - 1))->map(function ($i) use ($start, $counts) {
            $month = $start->addMonths($i);

            return ['month' => $month, 'count' => $counts[$month->format('Y-m')] ?? 0];
        });
    }

    /** Usuarios reales más recientes. */
    public function latestUsers(int $limit = 8): Collection
    {
        return User::where('is_demo', false)->withCount('cars')->latest()->limit($limit)->get();
    }

    private function records(): Builder
    {
        return MaintenanceRecord::query()
            ->join('cars', 'cars.id', '=', 'maintenance_records.car_id')
            ->join('users', 'users.id', '=', 'cars.user_id')
            ->when(! $this->includeDemo, fn ($q) => $q->where('users.is_demo', false));
    }

    private function cars(): Builder
    {
        return Car::query()->when(! $this->includeDemo, fn ($q) => $q->whereHas('user', fn ($u) => $u->where('is_demo', false)));
    }
}
