<?php

namespace Database\Factories;

use App\Models\Floor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Floor>
 */
class FloorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->word() . ' Floor',
            'number' => fake()->unique()->numerify('F-###'),
            'created_by' => User::inRandomOrder()->first()->id ?? User::factory(),
        ];
    }
}
