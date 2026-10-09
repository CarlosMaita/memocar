<?php

use App\Models\Car;
use App\Models\MaintenanceRecord;
use App\Models\MaintenanceType;

beforeEach(function () {
    $this->car = Car::factory()->create(['current_km' => 10000]);
    $this->user = $this->car->user;
    $this->type = MaintenanceType::factory()->for($this->user)->create();
});

test('registrar un mantenimiento actualiza el kilometraje del carro', function () {
    $this->actingAs($this->user)->post(route('records.store'), [
        'date' => now()->toDateString(),
        'maintenance_type_id' => $this->type->id,
        'km' => 12500,
        'cost' => '45.50',
        'description' => '',
    ])->assertRedirect(route('records.index'));

    $record = $this->car->records()->first();
    expect($record->cost)->toBe('45.50')
        ->and($record->description)->toBeNull()
        ->and($this->car->fresh()->current_km)->toBe(12500);
});

test('un registro con km menor no baja el kilometraje del carro', function () {
    MaintenanceRecord::factory()->for($this->car)->for($this->type, 'type')->create(['km' => 8000]);

    expect($this->car->fresh()->current_km)->toBe(10000);
});

test('no se puede usar un concepto de otro usuario', function () {
    $foreignType = MaintenanceType::factory()->create();

    $this->actingAs($this->user)->post(route('records.store'), [
        'date' => now()->toDateString(),
        'maintenance_type_id' => $foreignType->id,
        'cost' => 10,
    ])->assertSessionHasErrors('maintenance_type_id');
});

test('no se pueden ver ni editar registros de otro carro', function () {
    $foreign = MaintenanceRecord::factory()->create();

    $this->actingAs($this->user)->get(route('records.edit', $foreign))->assertNotFound();
    $this->actingAs($this->user)->delete(route('records.destroy', $foreign))->assertNotFound();
    $this->actingAs($this->user)->get(route('records.index'))->assertOk()->assertDontSee($foreign->type->name);
});

test('el listado se filtra por el carro seleccionado', function () {
    $other = Car::factory()->for($this->user)->create();
    $otherType = MaintenanceType::factory()->for($this->user)->create(['name' => 'Solo del otro carro']);
    MaintenanceRecord::factory()->for($other)->for($otherType, 'type')->create();
    MaintenanceRecord::factory()->for($this->car)->for($this->type, 'type')->create();

    $this->actingAs($this->user)->post(route('cars.choose', $this->car));
    $this->actingAs($this->user)->get(route('records.index'))
        ->assertOk()->assertSee($this->type->name)->assertDontSee('Solo del otro carro');
});
