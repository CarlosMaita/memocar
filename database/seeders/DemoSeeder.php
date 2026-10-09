<?php

namespace Database\Seeders;

use App\Actions\SeedMaintenanceCatalog;
use App\Models\User;
use Illuminate\Database\Seeder;

/** Datos de ejemplo para desarrollo local: demo@memocar.test / password */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'demo@memocar.test'],
            ['name' => 'Demo', 'password' => 'password', 'email_verified_at' => now()],
        );
        (new SeedMaintenanceCatalog)($user);

        if ($user->cars()->exists()) {
            return;
        }

        $type = fn (string $name) => $user->maintenanceTypes()->where('name', $name)->value('id');

        $corolla = $user->cars()->create([
            'name' => 'Corolla', 'brand' => 'Toyota', 'model' => 'Corolla', 'year' => 2015, 'plate' => 'AB123CD', 'current_km' => 0,
        ]);
        foreach ([
            ['Cambio de aceite y filtro de aceite', 9, 78000, 45, 'Aceite 15W40 semisintético'],
            ['Cambio de aceite y filtro de aceite', 4, 83500, 48, null],
            ['Cambio de filtro de aire del motor', 13, 74000, 12, null],
            ['Cambio de pastillas de freno delanteras', 20, 62000, 60, 'Pastillas cerámicas'],
            ['Rotación de cauchos', 7, 80000, 15, null],
            ['Cambio de batería', 50, null, 120, 'Batería 42 meses'],
            ['Cambio de empacadura de tapa de válvulas', 2, 84500, 35, 'Fuga de aceite leve'],
        ] as [$name, $monthsAgo, $km, $cost, $desc]) {
            $corolla->records()->create([
                'maintenance_type_id' => $type($name),
                'date' => now()->subMonths($monthsAgo)->toDateString(),
                'km' => $km,
                'cost' => $cost,
                'description' => $desc,
            ]);
        }
        $corolla->update(['current_km' => 86000]);

        $user->cars()->create(['name' => 'Moto de trabajo', 'brand' => 'Yamaha', 'model' => 'YBR 125', 'year' => 2020, 'current_km' => 15000]);
    }
}
