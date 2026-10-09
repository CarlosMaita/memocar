<?php

namespace App\Services;

use App\Models\Car;
use App\Models\MaintenanceType;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

/**
 * Calcula el próximo mantenimiento de cada falla/mantenimiento de un carro a partir
 * del último registro realizado y del intervalo seteado (km y/o meses, lo que ocurra primero).
 */
class MaintenanceSchedule
{
    public const OVERDUE = 'overdue';

    public const SOON = 'soon';

    public const OK = 'ok';

    /** Umbrales para marcar un mantenimiento como "próximo". */
    public const SOON_DAYS = 30;

    public const SOON_KM = 1000;

    /**
     * @return Collection<int, ScheduleItem> ordenados por urgencia
     */
    public function forCar(Car $car, ?CarbonImmutable $today = null): Collection
    {
        $today ??= CarbonImmutable::today();

        $lastByType = $car->records()
            ->orderByDesc('date')->orderByDesc('km')->orderByDesc('id')
            ->get()
            ->unique('maintenance_type_id')
            ->keyBy('maintenance_type_id');

        return $this->typesWithInterval($car)
            ->filter(fn (MaintenanceType $type) => $lastByType->has($type->id))
            ->map(function (MaintenanceType $type) use ($lastByType, $car, $today) {
                $last = $lastByType[$type->id];

                $nextDate = $type->set_months !== null
                    ? CarbonImmutable::parse($last->date)->addMonthsNoOverflow($type->set_months)
                    : null;
                $nextKm = ($type->set_km !== null && $last->km !== null) ? $last->km + $type->set_km : null;

                $daysLeft = $nextDate ? (int) $today->diffInDays($nextDate, false) : null;
                $kmLeft = $nextKm !== null ? $nextKm - $car->current_km : null;

                $lastDate = CarbonImmutable::parse($last->date);
                $fractions = [];
                if ($nextDate) {
                    $fractions[] = $lastDate->diffInDays($today, false) / max(1, $lastDate->diffInDays($nextDate));
                }
                if ($nextKm !== null) {
                    $fractions[] = ($car->current_km - $last->km) / $type->set_km;
                }
                $progress = (int) round(min(1, max(0, ...($fractions ?: [0]))) * 100);

                return new ScheduleItem($type, $last, $nextDate, $nextKm, $daysLeft, $kmLeft, $this->status($daysLeft, $kmLeft), $progress);
            })
            ->sortBy([
                fn (ScheduleItem $a, ScheduleItem $b) => $this->rank($a->status) <=> $this->rank($b->status),
                fn (ScheduleItem $a, ScheduleItem $b) => ($a->daysLeft ?? PHP_INT_MAX) <=> ($b->daysLeft ?? PHP_INT_MAX),
                fn (ScheduleItem $a, ScheduleItem $b) => ($a->kmLeft ?? PHP_INT_MAX) <=> ($b->kmLeft ?? PHP_INT_MAX),
            ])
            ->values();
    }

    /**
     * Mantenimientos con intervalo que todavía no tienen ningún registro en el carro.
     *
     * @return Collection<int, MaintenanceType>
     */
    public function neverDone(Car $car): Collection
    {
        $done = $car->records()->distinct()->pluck('maintenance_type_id')->all();

        return $this->typesWithInterval($car)
            ->reject(fn (MaintenanceType $type) => in_array($type->id, $done))
            ->values();
    }

    public function status(?int $daysLeft, ?int $kmLeft): string
    {
        if (($daysLeft !== null && $daysLeft < 0) || ($kmLeft !== null && $kmLeft < 0)) {
            return self::OVERDUE;
        }
        if (($daysLeft !== null && $daysLeft <= self::SOON_DAYS) || ($kmLeft !== null && $kmLeft <= self::SOON_KM)) {
            return self::SOON;
        }

        return self::OK;
    }

    private function rank(string $status): int
    {
        return [self::OVERDUE => 0, self::SOON => 1, self::OK => 2][$status];
    }

    private function typesWithInterval(Car $car): Collection
    {
        return MaintenanceType::where('user_id', $car->user_id)
            ->where('is_active', true)
            ->where(fn ($q) => $q->whereNotNull('set_km')->orWhereNotNull('set_months'))
            ->orderBy('category')->orderBy('name')
            ->get();
    }
}
