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
            'name' => $this->faker->word() . ' Floor',
            'number' => $this->faker->unique()->numerify('F-###'),
            'created_by' => User::inRandomOrder()->first()->id ?? User::factory(),
        ];
    }
}
