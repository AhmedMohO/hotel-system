<?php

namespace Database\Seeders;

use App\Models\Floor;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;

class FloorRoomSeeder extends Seeder
{
    public function run(): void
    {
        // Use the admin user as the creator
        $admin = User::first();

        $floors = [
            ['name' => 'Ground Floor', 'number' => '1000'],
            ['name' => 'First Floor',  'number' => '1001'],
            ['name' => 'Second Floor', 'number' => '1002'],
        ];

        foreach ($floors as $floorData) {
            $floor = Floor::create([
                'name'       => $floorData['name'],
                'number'     => $floorData['number'],
                'created_by' => $admin->id,
            ]);

            // Create 3 rooms per floor
            for ($i = 1; $i <= 3; $i++) {
                Room::create([
                    'number'     => $floor->number . '0' . $i, // e.g. 10001, 10002
                    'floor_id'   => $floor->id,
                    'capacity'   => rand(1, 5),
                    'price'      => rand(50, 300) * 100, // stored in cents
                    'created_by' => $admin->id,
                ]);
            }
        }
    }
}