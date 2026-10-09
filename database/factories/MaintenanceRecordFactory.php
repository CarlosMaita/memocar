<?php

namespace Database\Factories;

use App\Models\Car;
use App\Models\MaintenanceRecord;
use App\Models\MaintenanceType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MaintenanceRecord>
 */
class MaintenanceRecordFactory extends Factory
{
    public function definition(): array
    {
        return [
            'car_id' => Car::factory(),
            'maintenance_type_id' => MaintenanceType::factory(),
            'date' => now()->toDateString(),
            'km' => null,
            'cost' => 10,
        ];
    }
}
