<?php

use App\Actions\CreateDemoUser;
use App\Models\Car;
use App\Models\MaintenanceRecord;
use App\Models\MaintenanceType;
use App\Models\User;
use App\Services\AdminStats;

function admin(): User
{
    return tap(User::factory()->create()->forceFill(['is_admin' => true]))->save();
}

test('solo un administrador entra al panel', function () {
    $this->get('/admin')->assertRedirect('/login');
    $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden();
    $this->actingAs(admin())->get('/admin')->assertOk()->assertSee('Espectro de fallas');
});

test('el comando memocar:admin crea el administrador', function () {
    $this->artisan('memocar:admin', ['email' => 'jefe@example.com', '--password' => 'secreta-123'])->assertSuccessful();

    $user = User::where('email', 'jefe@example.com')->first();
    expect($user->is_admin)->toBeTrue()->and(Hash::check('secreta-123', $user->password))->toBeTrue();
});

test('las estadísticas cuentan usuarios, carros y registros y excluyen demo', function () {
    $user = User::factory()->create();
    $car = Car::factory()->for($user)->create();
    $oil = MaintenanceType::factory()->for($user)->create(['name' => 'Cambio de aceite', 'category' => 'Motor']);
    $brakes = MaintenanceType::factory()->for($user)->create(['name' => 'Frenos', 'category' => 'Frenos']);
    MaintenanceRecord::factory()->count(3)->for($car)->for($oil, 'type')->create(['cost' => 40]);
    MaintenanceRecord::factory()->for($car)->for($brakes, 'type')->create(['cost' => 60]);
    (new CreateDemoUser)();

    $stats = new AdminStats;
    $kpis = $stats->kpis();
    expect($kpis['users'])->toBe(1)->and($kpis['cars'])->toBe(1)->and($kpis['records'])->toBe(4)
        ->and($kpis['cost'])->toBe(180.0)->and($kpis['demoActive'])->toBe(1);

    $spectrum = $stats->spectrum();
    expect($spectrum->first())->toMatchArray(['name' => 'Cambio de aceite', 'count' => 3, 'share' => 75.0, 'avgCost' => 40.0])
        ->and($spectrum->last()['cumulative'])->toEqual(100);

    expect((new AdminStats(includeDemo: true))->kpis()['records'])->toBeGreaterThan(4);
});

test('el espectrograma agrupa por categoría y mes', function () {
    $car = Car::factory()->create();
    $type = MaintenanceType::factory()->for($car->user)->create(['category' => 'Motor']);
    MaintenanceRecord::factory()->count(2)->for($car)->for($type, 'type')->create(['date' => now()->toDateString()]);

    $grid = (new AdminStats)->spectrogram();
    expect($grid['months'])->toHaveCount(12)
        ->and($grid['rows']->first()['category'])->toBe('Motor')
        ->and(end($grid['rows']->first()['cells']))->toBe(2);
});
