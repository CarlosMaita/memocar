<?php

use App\Models\Car;
use App\Models\User;

test('sin carros el usuario va a crear su primer carro', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/dashboard')->assertRedirect(route('cars.select'));
    $this->actingAs($user)->get(route('cars.select'))->assertRedirect(route('cars.create'));
});

test('con un solo carro se selecciona automáticamente', function () {
    $car = Car::factory()->create();

    $this->actingAs($car->user)->get('/dashboard')->assertOk()->assertSee($car->name);
});

test('con varios carros pide elegir y luego muestra el dashboard del elegido', function () {
    $user = User::factory()->create();
    $a = Car::factory()->for($user)->create(['name' => 'Corolla']);
    $b = Car::factory()->for($user)->create(['name' => 'Aveo']);

    $this->actingAs($user)->get('/dashboard')->assertRedirect(route('cars.select'));
    $this->actingAs($user)->get(route('cars.select'))->assertOk()->assertSee('Corolla')->assertSee('Aveo');

    $this->actingAs($user)->post(route('cars.choose', $b))->assertRedirect(route('dashboard'));
    $this->actingAs($user)->get('/dashboard')->assertOk()->assertSee('Aveo');
});

test('crear un carro lo deja seleccionado', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('cars.store'), ['name' => 'Fiesta', 'current_km' => ''])
        ->assertRedirect(route('dashboard'));

    expect($user->cars()->first()->current_km)->toBe(0);
    $this->actingAs($user)->get('/dashboard')->assertOk()->assertSee('Fiesta');
});

test('no se puede seleccionar ni editar el carro de otro usuario', function () {
    $user = User::factory()->create();
    Car::factory()->for($user)->create();
    $foreign = Car::factory()->create();

    $this->actingAs($user)->post(route('cars.choose', $foreign))->assertNotFound();
    $this->actingAs($user)->get(route('cars.edit', $foreign))->assertNotFound();
    $this->actingAs($user)->delete(route('cars.destroy', $foreign))->assertNotFound();
});
