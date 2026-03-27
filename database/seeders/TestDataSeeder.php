<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first user for approvals
        $approver = User::first();

        $countries = ['Egypt', 'Saudi Arabia', 'UAE', 'Jordan', 'Kuwait', 'Bahrain', 'Qatar', 'Oman', 'Lebanon', 'Palestine'];
        $firstNames = ['Ahmed', 'Mohamed', 'Ali', 'Sara', 'Layla', 'Omar', 'Noor', 'Tarek', 'Hana', 'Khalid'];
        $lastNames = ['Hassan', 'Ibrahim', 'Ali', 'Khaled', 'Rahman', 'Ahmed', 'Mansour', 'Saleh', 'Youssef', 'Malik'];

        // Create 50 test clients
        $clients = [];
        for ($i = 0; $i < 50; $i++) {
            $gender = $i % 2 === 0 ? 'Male' : 'Female';
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $country = $countries[array_rand($countries)];

            $client = Client::create([
                'name' => $firstName . ' ' . $lastName,
                'email' => strtolower($firstName . '.' . $lastName . $i . '@example.com'),
                'password' => bcrypt('password'),
                'mobile' => '00201' . rand(100000000, 999999999),
                'country' => $country,
                'gender' => $gender,
                'approved_by' => $approver->id,
                'approved_at' => now(),
                'last_login_at' => now()->subDays(rand(0, 60)),
            ]);

            $clients[] = $client;
        }

        // Get available rooms
        $rooms = Room::all()->toArray();

        if (empty($rooms)) {
            return; // Exit if no rooms exist
        }

        // Create 150 test reservations spread across 2026
        for ($i = 0; $i < 150; $i++) {
            $client = $clients[array_rand($clients)];
            $room = $rooms[array_rand($rooms)];

            // Distribute reservations across all 12 months of 2026
            $month = rand(1, 12);
            $day = rand(1, 28);
            $reservationDate = now()->setYear(2026)->setMonth($month)->setDay($day);

            // Vary prices from $50 to $500
            $paidPrice = rand(5000, 50000); // In cents

            Reservation::create([
                'client_id' => $client->id,
                'room_id' => $room['id'],
                'accompany_number' => rand(1, 4),
                'paid_price' => $paidPrice,
                'approved_by' => $approver->id,
                'created_at' => $reservationDate,
                'updated_at' => $reservationDate,
            ]);
        }
    }
}
