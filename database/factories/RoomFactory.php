<?php

namespace Database\Factories;

use App\Models\Room;
use App\Models\Floor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    public function definition(): array
    {
        return [
            'number' => $this->faker->unique()->numerify('R-###'),
            'floor_id' => Floor::inRandomOrder()->value('id') ?? Floor::factory(),
            'capacity' => $this->faker->numberBetween(1, 4),
            'price' => $this->faker->numberBetween(100, 500) * 100,
            'created_by' => User::inRandomOrder()->value('id') ?? User::factory(),
        ];
    }
}
