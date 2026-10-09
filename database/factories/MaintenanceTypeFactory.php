<?php

namespace Database\Factories;

use App\Models\MaintenanceType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MaintenanceType>
 */
class MaintenanceTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->unique()->sentence(3),
            'category' => 'General',
            'recommended_km' => 5000,
            'recommended_months' => 6,
            'set_km' => 5000,
            'set_months' => 6,
        ];
    }
}
