<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Cuenta demo desechable: una por visitante, con catálogo y dos carros de ejemplo.
 * Se borran a las 24 h (ver purgeExpired), así nadie pisa los datos de otro.
 */
class CreateDemoUser
{
    public const TTL_HOURS = 24;

    public function __invoke(): User
    {
        $this->purgeExpired();

        return DB::transaction(function () {
            $user = (new User)->forceFill([
                'name' => 'Invitado',
                'email' => 'demo-'.Str::lower(Str::random(12)).'@demo.memocar.invalid',
                'password' => Str::random(40),
                'email_verified_at' => now(),
                'is_demo' => true,
            ]);
            $user->save();

            (new SeedMaintenanceCatalog)($user);
            (new SeedDemoGarage)($user);

            return $user;
        });
    }

    public function purgeExpired(): int
    {
        $expired = User::where('is_demo', true)->where('created_at', '<', now()->subHours(self::TTL_HOURS))->get();

        foreach ($expired as $user) {
            // Primero los carros (arrastran sus registros), luego el usuario y su catálogo.
            DB::transaction(function () use ($user) {
                $user->cars()->delete();
                DB::table('sessions')->where('user_id', $user->id)->delete();
                $user->delete();
            });
        }

        return $expired->count();
    }
}
