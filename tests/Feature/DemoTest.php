<?php

use App\Actions\CreateDemoUser;
use App\Models\MaintenanceRecord;
use App\Models\User;

test('el login muestra el botón Ver como demo', function () {
    $this->get('/login')->assertOk()->assertSee('Ver como demo');
});

test('Ver como demo crea una cuenta propia con dos carros e historial', function () {
    $this->post(route('demo.start'))->assertRedirect(route('cars.select'));

    $user = auth()->user();
    expect($user->is_demo)->toBeTrue()
        ->and($user->cars()->count())->toBe(2)
        ->and($user->maintenanceTypes()->count())->toBeGreaterThan(30);

    $aveo = $user->cars()->where('name', 'Aveo')->first();
    expect($aveo->records()->count())->toBe(10)->and($aveo->current_km)->toBe(152300);

    $this->post(route('cars.choose', $aveo));
    $this->get('/dashboard')->assertOk()->assertSee('Modo demo')->assertSee('Vencido');
});

test('cada visitante recibe su propia cuenta demo', function () {
    $this->post(route('demo.start'));
    $first = auth()->id();
    $this->post(route('logout'));

    $this->post(route('demo.start'));
    expect(auth()->id())->not->toBe($first)
        ->and(User::where('is_demo', true)->count())->toBe(2);
});

test('una cuenta demo no puede cambiar perfil, contraseña ni borrarse', function () {
    $this->post(route('demo.start'));

    $this->get(route('profile.edit'))->assertRedirect(route('dashboard'));
    $this->patch(route('profile.update'), ['name' => 'X', 'email' => 'real@example.com'])->assertRedirect(route('dashboard'));
    $this->put(route('password.update'), ['current_password' => 'x', 'password' => 'nueva1234', 'password_confirmation' => 'nueva1234'])->assertRedirect(route('dashboard'));
    $this->delete(route('profile.destroy'), ['password' => 'x'])->assertRedirect(route('dashboard'));

    expect(auth()->user()->fresh()->email)->toEndWith('@demo.memocar.invalid');
});

test('las cuentas demo vencidas se borran con sus datos y las reales no', function () {
    $real = User::factory()->create(['created_at' => now()->subDays(3)]);
    $recent = (new CreateDemoUser)();
    $old = (new CreateDemoUser)();
    $old->forceFill(['created_at' => now()->subHours(25)])->save();
    $oldCars = $old->cars()->pluck('id');

    expect((new CreateDemoUser)->purgeExpired())->toBe(1)
        ->and(User::find($old->id))->toBeNull()
        ->and(User::find($recent->id))->not->toBeNull()
        ->and(User::find($real->id))->not->toBeNull()
        ->and(MaintenanceRecord::whereIn('car_id', $oldCars)->count())->toBe(0);
});

test('salir del demo lleva al registro', function () {
    $this->post(route('demo.start'));

    $this->post(route('demo.exit'))->assertRedirect(route('register'));
    $this->assertGuest();
});
