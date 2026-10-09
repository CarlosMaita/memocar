<?php

namespace App\Services;

use App\Models\MaintenanceRecord;
use App\Models\MaintenanceType;
use Carbon\CarbonImmutable;

final readonly class ScheduleItem
{
    public function __construct(
        public MaintenanceType $type,
        public MaintenanceRecord $lastRecord,
        public ?CarbonImmutable $nextDate,
        public ?int $nextKm,
        public ?int $daysLeft,
        public ?int $kmLeft,
        public string $status,
        /** Porcentaje del intervalo consumido (0-100), el mayor entre km y tiempo. */
        public int $progress = 0,
    ) {}

    public function statusLabel(): string
    {
        return match ($this->status) {
            MaintenanceSchedule::OVERDUE => 'Vencido',
            MaintenanceSchedule::SOON => 'Próximo',
            default => 'Al día',
        };
    }

    /** "Venció hace 30 días · faltan 3.000 km", "Faltan 61 días · 2.500 km", … */
    public function remainingLabel(): string
    {
        $parts = [];
        if ($this->daysLeft !== null) {
            $days = number_format(abs($this->daysLeft), 0, ',', '.');
            $parts[] = $this->daysLeft < 0 ? "venció hace {$days} días" : "faltan {$days} días";
        }
        if ($this->kmLeft !== null) {
            $prefix = $this->kmLeft < 0 ? 'pasado por ' : ($this->daysLeft === null || $this->daysLeft < 0 ? 'faltan ' : '');
            $parts[] = $prefix.km(abs($this->kmLeft));
        }

        return $parts ? ucfirst(implode(' · ', $parts)) : 'Registra el km para calcular';
    }
}
