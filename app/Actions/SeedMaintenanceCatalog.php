<?php

namespace App\Actions;

use App\Models\User;

class SeedMaintenanceCatalog
{
    /**
     * Copia el catálogo base al usuario. Idempotente: solo inserta los nombres que le faltan.
     */
    public function __invoke(User $user): int
    {
        $existing = $user->maintenanceTypes()->pluck('name')->all();
        $created = 0;

        foreach (self::catalog() as [$name, $category, $km, $months, $notes]) {
            if (in_array($name, $existing, true)) {
                continue;
            }

            $user->maintenanceTypes()->create([
                'name' => $name,
                'category' => $category,
                'recommended_km' => $km,
                'recommended_months' => $months,
                'set_km' => $km,
                'set_months' => $months,
                'notes' => $notes,
            ]);
            $created++;
        }

        return $created;
    }

    public static function catalog(): array
    {
        return require database_path('data/maintenance_catalog.php');
    }
}
