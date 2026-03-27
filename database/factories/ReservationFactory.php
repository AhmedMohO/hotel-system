<?php

namespace Database\Factories;

use App\Models\Reservation;
use App\Models\Client;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'client_id' => Client::inRandomOrder()->value('id') ?? Client::factory(),
            'room_id' => Room::inRandomOrder()->value('id') ?? Room::factory(),
            'accompany_number' => fake()->numberBetween(0, 3),
            'paid_price' => fake()->numberBetween(100, 1500) * 100,
            'approved_by' => User::inRandomOrder()->value('id') ?? User::factory(),
        ];
    }
}
