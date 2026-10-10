<?php

namespace Database\Seeders;

use App\Actions\SeedDemoGarage;
use App\Actions\SeedMaintenanceCatalog;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Datos de prueba: usuario demo@memocar.test / password con dos carros e historial.
 * En producción no se corre (ver DatabaseSeeder); allí el botón "Ver como demo" crea cuentas desechables.
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'demo@memocar.test'],
            ['name' => 'Demo', 'password' => 'password', 'email_verified_at' => now()],
        );
        (new SeedMaintenanceCatalog)($user);

        if (! $user->cars()->exists()) {
            (new SeedDemoGarage)($user);
        }
    }
}
