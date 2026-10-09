<?php

namespace Database\Seeders;

use App\Actions\SeedMaintenanceCatalog;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Completa el catálogo base de fallas/mantenimientos de todos los usuarios
 * (los nuevos lo reciben al registrarse). Seguro de correr varias veces.
 */
class MaintenanceCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $seed = new SeedMaintenanceCatalog;

        User::each(fn (User $user) => $seed($user));
    }
}
