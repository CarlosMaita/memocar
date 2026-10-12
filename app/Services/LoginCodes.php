<?php

namespace App\Services;

use App\Mail\LoginCodeMail;
use App\Models\LoginCode;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * Acceso sin contraseña: código de 6 dígitos + enlace mágico por correo.
 * Un solo código vigente por usuario; se guarda hasheado, vence y se invalida tras varios intentos.
 */
class LoginCodes
{
    public const TTL_MINUTES = 10;

    public const MAX_ATTEMPTS = 5;

    public const RESEND_SECONDS = 60;

    /** Las cuentas demo y de administración no entran por código. */
    public function allowedFor(?User $user): bool
    {
        return $user !== null && ! $user->is_demo && ! $user->is_admin;
    }

    public function send(User $user): void
    {
        LoginCode::where('user_id', $user->id)->whereNull('consumed_at')->delete();
        LoginCode::where('expires_at', '<', now()->subDay())->delete();

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $link = Str::random(64);

        $user->loginCodes()->create([
            'code_hash' => Hash::make($code),
            'link_hash' => hash('sha256', $link),
            'expires_at' => now()->addMinutes(self::TTL_MINUTES),
        ]);

        Mail::to($user)->send(new LoginCodeMail($user, $code, route('login.link', $link)));
    }

    /** Valida el código; suma un intento si falla. Devuelve el usuario si es correcto. */
    public function verify(User $user, string $code): ?User
    {
        $login = $user->loginCodes()->whereNull('consumed_at')->latest('id')->first();

        if (! $login || ! $login->isUsable()) {
            return null;
        }

        if (! Hash::check($code, $login->code_hash)) {
            $login->increment('attempts');

            return null;
        }

        return $this->consume($login);
    }

    public function verifyLink(string $link): ?User
    {
        $login = LoginCode::where('link_hash', hash('sha256', $link))->first();

        return $login && $login->isUsable() ? $this->consume($login) : null;
    }

    private function consume(LoginCode $login): User
    {
        $login->forceFill(['consumed_at' => now()])->save();

        $user = $login->user;
        if (! $user->email_verified_at) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        return $user;
    }
}
