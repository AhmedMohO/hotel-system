<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Floor;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClientReservationSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        // Require at least one user to assign as 'created_by' / 'approved_by'
        if (User::count() === 0) {
            User::factory()->create();
        }

        // Create 5 Floors, each with 10 Rooms
        Floor::factory()
            ->count(5)
            ->has(Room::factory()->count(10))
            ->create();

        // Create 50 Clients
        $clients = Client::factory()->count(50)->create();

        // Create 1-3 Reservations per Client
        $clients->each(function (Client $client) use ($faker) {
            Reservation::factory()
                ->count($faker->numberBetween(1, 3))
                ->create([
                    'client_id' => $client->id,
                ]);
        });
    }
}
