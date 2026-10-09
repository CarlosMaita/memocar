<?php

use App\Models\Car;
use App\Models\MaintenanceRecord;
use App\Models\MaintenanceType;
use App\Services\MaintenanceSchedule;
use Carbon\CarbonImmutable;

beforeEach(function () {
    $this->today = CarbonImmutable::parse('2026-10-09');
    $this->car = Car::factory()->create(['current_km' => 0]);
    $this->schedule = new MaintenanceSchedule;
});

function record(Car $car, MaintenanceType $type, string $date, ?int $km): MaintenanceRecord
{
    return MaintenanceRecord::factory()->for($car)->for($type, 'type')->create(['date' => $date, 'km' => $km]);
}

test('calcula la próxima fecha y km desde el último registro', function () {
    $oil = MaintenanceType::factory()->for($this->car->user)->create(['set_km' => 5000, 'set_months' => 6]);
    record($this->car, $oil, '2026-01-10', 70000);
    record($this->car, $oil, '2026-07-09', 80000); // el último manda
    $this->car->update(['current_km' => 82000]);

    $item = $this->schedule->forCar($this->car, $this->today)->sole();

    expect($item->nextDate->toDateString())->toBe('2027-01-09')
        ->and($item->nextKm)->toBe(85000)
        ->and($item->kmLeft)->toBe(3000)
        ->and($item->daysLeft)->toBe(92)
        ->and($item->status)->toBe(MaintenanceSchedule::OK);
});

test('vencido por km aunque la fecha esté al día', function () {
    $oil = MaintenanceType::factory()->for($this->car->user)->create(['set_km' => 5000, 'set_months' => 6]);
    record($this->car, $oil, '2026-09-01', 80000);
    $this->car->update(['current_km' => 85200]);

    expect($this->schedule->forCar($this->car, $this->today)->sole()->status)->toBe(MaintenanceSchedule::OVERDUE);
});

test('vencido por fecha', function () {
    $battery = MaintenanceType::factory()->for($this->car->user)->create(['set_km' => null, 'set_months' => 48]);
    record($this->car, $battery, '2022-01-01', null);

    $item = $this->schedule->forCar($this->car, $this->today)->sole();
    expect($item->status)->toBe(MaintenanceSchedule::OVERDUE)->and($item->nextKm)->toBeNull();
});

test('próximo cuando faltan menos de 30 días o 1.000 km', function () {
    $user = $this->car->user;
    $byDate = MaintenanceType::factory()->for($user)->create(['set_km' => null, 'set_months' => 6]);
    $byKm = MaintenanceType::factory()->for($user)->create(['set_km' => 10000, 'set_months' => null]);
    record($this->car, $byDate, '2026-04-20', null);
    record($this->car, $byKm, '2026-10-01', 50000);
    $this->car->update(['current_km' => 59500]);

    $items = $this->schedule->forCar($this->car, $this->today);
    expect($items->pluck('status')->all())->toBe([MaintenanceSchedule::SOON, MaintenanceSchedule::SOON]);
});

test('ordena por urgencia e ignora conceptos sin registro, sin intervalo o inactivos', function () {
    $user = $this->car->user;
    $ok = MaintenanceType::factory()->for($user)->create(['set_km' => null, 'set_months' => 24]);
    $overdue = MaintenanceType::factory()->for($user)->create(['set_km' => null, 'set_months' => 1]);
    $noInterval = MaintenanceType::factory()->for($user)->create(['set_km' => null, 'set_months' => null]);
    $inactive = MaintenanceType::factory()->for($user)->create(['is_active' => false]);
    $neverDone = MaintenanceType::factory()->for($user)->create();
    foreach ([$ok, $overdue, $noInterval, $inactive] as $type) {
        record($this->car, $type, '2026-06-01', null);
    }

    $items = $this->schedule->forCar($this->car, $this->today);

    expect($items->map(fn ($i) => $i->type->id)->all())->toBe([$overdue->id, $ok->id])
        ->and($this->schedule->neverDone($this->car)->pluck('id')->all())->toBe([$neverDone->id]);
});

test('los registros de otro carro no afectan', function () {
    $oil = MaintenanceType::factory()->for($this->car->user)->create();
    $other = Car::factory()->for($this->car->user)->create();
    record($other, $oil, '2026-01-01', 1000);

    expect($this->schedule->forCar($this->car, $this->today))->toBeEmpty();
});

test('el dashboard muestra el estado', function () {
    $oil = MaintenanceType::factory()->for($this->car->user)->create(['name' => 'Cambio de aceite', 'set_km' => 5000, 'set_months' => 6]);
    record($this->car, $oil, now()->subMonths(7)->toDateString(), 1000);

    $this->actingAs($this->car->user)->get('/dashboard')
        ->assertOk()->assertSee('Cambio de aceite')->assertSee('Vencido');
});
