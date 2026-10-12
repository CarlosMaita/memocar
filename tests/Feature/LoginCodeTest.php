<?php

use App\Mail\LoginCodeMail;
use App\Models\LoginCode;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

beforeEach(fn () => Mail::fake());

function requestCode($test, string $email): ?LoginCodeMail
{
    $test->post(route('login.code.send'), ['email' => $email])->assertRedirect(route('login.code'));

    return Mail::sent(LoginCodeMail::class)->last();
}

test('se entra con el código que llega por correo', function () {
    $user = User::factory()->create(['email' => 'ana@example.com']);

    $mail = requestCode($this, 'ana@example.com');
    expect($mail->code)->toMatch('/^\d{6}$/');
    $this->get(route('login.code'))->assertOk()->assertSee('a••@example.com', false);

    $this->post(route('login.code.verify'), ['code' => $mail->code])->assertRedirect('/dashboard');
    $this->assertAuthenticatedAs($user);
});

test('el código sirve una sola vez y el incorrecto no entra', function () {
    User::factory()->create(['email' => 'ana@example.com']);
    $mail = requestCode($this, 'ana@example.com');

    $this->post(route('login.code.verify'), ['code' => $mail->code === '000000' ? '111111' : '000000'])->assertSessionHasErrors('code');
    $this->assertGuest();

    $this->post(route('login.code.verify'), ['code' => $mail->code]);
    $this->assertAuthenticated();
    $this->post(route('logout'));

    $this->withSession(['login_code.email' => 'ana@example.com'])
        ->post(route('login.code.verify'), ['code' => $mail->code])->assertSessionHasErrors('code');
    $this->assertGuest();
});

test('el código vence y se bloquea tras 5 intentos fallidos', function () {
    User::factory()->create(['email' => 'ana@example.com']);
    $mail = requestCode($this, 'ana@example.com');

    foreach (range(1, 5) as $i) {
        $this->post(route('login.code.verify'), ['code' => $mail->code === '999999' ? '888888' : '999999']);
    }
    $this->post(route('login.code.verify'), ['code' => $mail->code])->assertSessionHasErrors('code');
    $this->assertGuest();

    $this->travel(61)->seconds();
    $mail = requestCode($this, 'ana@example.com');
    $this->travel(11)->minutes();
    $this->post(route('login.code.verify'), ['code' => $mail->code])->assertSessionHasErrors('code');
    $this->assertGuest();
});

test('el enlace del correo pide confirmar y luego entra', function () {
    $user = User::factory()->create(['email' => 'ana@example.com']);
    $mail = requestCode($this, 'ana@example.com');
    $token = basename($mail->link);

    $this->get($mail->link)->assertOk()->assertSee('Entrar a MemoCar');
    $this->assertGuest();

    $this->post(route('login.link.use', $token))->assertRedirect('/dashboard');
    $this->assertAuthenticatedAs($user);
    $this->post(route('logout'));

    $this->post(route('login.link.use', $token))->assertRedirect(route('login'));
    $this->assertGuest();
});

test('no revela si el correo existe y no envía a demo ni admin', function () {
    $admin = tap(User::factory()->create(['email' => 'jefe@example.com'])->forceFill(['is_admin' => true]))->save();

    requestCode($this, 'nadie@example.com');
    $this->get(route('login.code'))->assertOk()->assertSee('Revisa tu correo');
    requestCode($this, 'jefe@example.com');

    Mail::assertNothingSent();
});

test('limita la cantidad de códigos por correo', function () {
    User::factory()->create(['email' => 'ana@example.com']);

    foreach (range(1, 3) as $i) {
        $this->post(route('login.code.send'), ['email' => 'ana@example.com'])->assertSessionHasNoErrors();
    }
    $this->post(route('login.code.send'), ['email' => 'ana@example.com'])->assertSessionHasErrors('email');
    Mail::assertSent(LoginCodeMail::class, 3);
    expect(LoginCode::whereNull('consumed_at')->count())->toBe(1);
});

test('registro sin contraseña confirma el correo con código', function () {
    $this->post('/register', ['name' => 'Luis', 'email' => 'luis@example.com'])->assertRedirect(route('login.code'));
    $this->assertGuest();

    $user = User::where('email', 'luis@example.com')->first();
    expect($user->hasPassword())->toBeFalse()->and($user->email_verified_at)->toBeNull();

    $mail = Mail::sent(LoginCodeMail::class)->last();
    $this->post(route('login.code.verify'), ['code' => $mail->code]);
    $this->assertAuthenticatedAs($user);
    expect($user->fresh()->email_verified_at)->not->toBeNull();
});

test('quien no tiene contraseña puede crearla sin la actual y borrar su cuenta escribiendo ELIMINAR', function () {
    $user = User::factory()->create(['password' => null]);

    $this->actingAs($user)->put(route('password.update'), ['password' => 'NuevaClave123', 'password_confirmation' => 'NuevaClave123'])
        ->assertSessionHasNoErrors();
    expect($user->fresh()->hasPassword())->toBeTrue();

    $other = User::factory()->create(['password' => null]);
    $this->actingAs($other)->delete(route('profile.destroy'), ['password' => 'no'])->assertSessionHasErrorsIn('userDeletion', 'password');
    $this->actingAs($other)->delete(route('profile.destroy'), ['password' => 'ELIMINAR'])->assertRedirect('/');
    expect(User::find($other->id))->toBeNull();
});

test('la contraseña sigue funcionando', function () {
    $user = User::factory()->create(['email' => 'ana@example.com']);

    $this->post('/login', ['email' => 'ana@example.com', 'password' => 'password'])->assertRedirect('/dashboard');
    $this->assertAuthenticatedAs($user);
});

test('con el acceso por código apagado todo sigue con contraseña', function () {
    config(['auth.login_codes' => false]);
    User::factory()->create(['email' => 'ana@example.com']);

    $this->get('/login')->assertOk()->assertDontSee('Enviarme un código')->assertSee('name="password"', false);
    $this->post(route('login.code.send'), ['email' => 'ana@example.com'])->assertNotFound();
    $this->post('/register', ['name' => 'Luis', 'email' => 'luis@example.com'])->assertSessionHasErrors('password');
    Mail::assertNothingSent();
});

test('el login con códigos activos muestra ambas opciones bien renderizadas', function () {
    $this->get('/login')->assertOk()
        ->assertSee('Enviarme un código')
        ->assertSee('Prefiero entrar con contraseña')
        ->assertSee('id="password" type="password" name="password"', false)
        ->assertDontSee('<x-text-input', false);
});
