<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Carbon;
use RuntimeException;

/**
 * Carga dos carros con historial de ejemplo (fechas relativas a hoy) para cuentas demo.
 * Los conceptos deben existir en el catálogo del usuario (SeedMaintenanceCatalog).
 */
class SeedDemoGarage
{
    /** [carro, km actual, [[concepto, meses atrás, km, costo, descripción], …]] */
    private const GARAGE = [
        [
            ['name' => 'Corolla', 'brand' => 'Toyota', 'model' => 'Corolla', 'year' => 2015, 'plate' => 'AB123CD'],
            86000,
            [
                ['Cambio de batería', 50, null, 120, 'Batería 42 meses'],
                ['Cambio de pastillas de freno delanteras', 20, 62000, 60, 'Pastillas cerámicas'],
                ['Cambio de filtro de aire del motor', 13, 74000, 12, null],
                ['Cambio de aceite y filtro de aceite', 9, 78000, 45, 'Aceite 15W40 semisintético'],
                ['Rotación de cauchos', 5, 80000, 15, null],
                ['Cambio de aceite y filtro de aceite', 4, 83500, 48, null],
                ['Cambio de empacadura de tapa de válvulas', 2, 84500, 35, 'Fuga de aceite leve'],
            ],
        ],
        [
            ['name' => 'Aveo', 'brand' => 'Chevrolet', 'model' => 'Aveo', 'year' => 2012, 'plate' => 'AC456EF'],
            152300,
            [
                ['Cambio de correa de tiempo (distribución)', 40, 112000, 180, 'Kit completo con tensor'],
                ['Cambio de bomba de agua', 40, 112000, 65, 'Junto con la correa de tiempo'],
                ['Cambio de cauchos', 30, 120500, 360, '4 cauchos 185/60 R14'],
                ['Cambio de batería', 26, 124000, 110, null],
                ['Cambio de refrigerante', 25, 125000, 25, null],
                ['Cambio de bujías', 14, 138000, 28, 'Bujías NGK'],
                ['Reparación no programada / Otro', 8, 143200, 140, 'Alternador reconstruido'],
                ['Cambio de aceite y filtro de aceite', 5, 147800, 40, 'Aceite 20W50 mineral'],
                ['Alineación y balanceo', 3, 149800, 30, null],
                ['Cambio de pastillas de freno delanteras', 1, 151500, 55, null],
            ],
        ],
    ];

    public function __invoke(User $user): void
    {
        $types = $user->maintenanceTypes()->pluck('id', 'name');

        foreach (self::GARAGE as [$attributes, $currentKm, $records]) {
            $car = $user->cars()->create($attributes + ['current_km' => 0]);

            foreach ($records as [$type, $monthsAgo, $km, $cost, $description]) {
                $car->records()->create([
                    'maintenance_type_id' => $types[$type] ?? throw new RuntimeException("Concepto demo inexistente: {$type}"),
                    'date' => Carbon::today()->subMonthsNoOverflow($monthsAgo)->toDateString(),
                    'km' => $km,
                    'cost' => $cost,
                    'description' => $description,
                ]);
            }

            $car->update(['current_km' => $currentKm]);
        }
    }
}
