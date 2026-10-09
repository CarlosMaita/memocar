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
    ) {}

    public function statusLabel(): string
    {
        return match ($this->status) {
            MaintenanceSchedule::OVERDUE => 'Vencido',
            MaintenanceSchedule::SOON => 'Próximo',
            default => 'Al día',
        };
    }
}
