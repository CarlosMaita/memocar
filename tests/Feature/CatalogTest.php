<?php

use App\Actions\SeedMaintenanceCatalog;
use App\Models\User;

test('al registrarse el usuario recibe el catálogo base con intervalo seteado = recomendado', function () {
    $this->post('/register', [
        'name' => 'Ana',
        'email' => 'ana@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $user = User::where('email', 'ana@example.com')->firstOrFail();
    $oil = $user->maintenanceTypes()->where('name', 'Cambio de aceite y filtro de aceite')->firstOrFail();

    expect($user->maintenanceTypes()->count())->toBe(count(SeedMaintenanceCatalog::catalog()))
        ->and($oil->recommended_km)->toBe(5000)
        ->and($oil->set_km)->toBe(5000)
        ->and($oil->set_months)->toBe(6);
});

test('el catálogo es idempotente', function () {
    $user = User::factory()->create();
    $seed = new SeedMaintenanceCatalog;

    $seed($user);
    expect($seed($user))->toBe(0);
});

test('el usuario puede ajustar el intervalo seteado y restablecerlo', function () {
    $user = User::factory()->create();
    (new SeedMaintenanceCatalog)($user);
    $user->cars()->create(['name' => 'Corolla']);
    $type = $user->maintenanceTypes()->where('name', 'Cambio de aceite y filtro de aceite')->first();

    $this->actingAs($user)->put(route('maintenance-types.update', $type), [
        'name' => $type->name, 'category' => $type->category, 'set_km' => 8000, 'set_months' => 12, 'is_active' => 1,
    ])->assertRedirect(route('maintenance-types.index'));

    expect($type->fresh()->set_km)->toBe(8000)->and($type->fresh()->recommended_km)->toBe(5000);

    $this->actingAs($user)->post(route('maintenance-types.reset', $type));
    expect($type->fresh()->set_km)->toBe(5000);
});

test('no se puede editar el catálogo de otro usuario', function () {
    $user = User::factory()->create();
    $user->cars()->create(['name' => 'Corolla']);
    $other = User::factory()->create();
    (new SeedMaintenanceCatalog)($other);

    $this->actingAs($user)
        ->get(route('maintenance-types.edit', $other->maintenanceTypes()->first()))
        ->assertNotFound();
});
