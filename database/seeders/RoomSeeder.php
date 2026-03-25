<?php

namespace Database\Seeders;

use App\Models\Floor;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $creatorId = User::query()->value('id');

        if (! $creatorId) {
            return;
        }

        $now = now();

        if (! Floor::query()->exists()) {
            Floor::query()->upsert([
                [
                    'name' => 'First Floor',
                    'number' => '1',
                    'created_by' => $creatorId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'name' => 'Second Floor',
                    'number' => '2',
                    'created_by' => $creatorId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ], ['number'], ['name', 'created_by', 'updated_at']);
        }

        $floorIds = Floor::query()->orderBy('number')->pluck('id')->values();

        if ($floorIds->isEmpty()) {
            return;
        }

        $firstFloorId = $floorIds->get(0);
        $secondFloorId = $floorIds->get(1) ?? $firstFloorId;

        Room::query()->upsert([
            [
                'number' => '101',
                'floor_id' => $firstFloorId,
                'capacity' => 2,
                'price' => 10000,
                'created_by' => $creatorId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'number' => '102',
                'floor_id' => $firstFloorId,
                'capacity' => 3,
                'price' => 14000,
                'created_by' => $creatorId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'number' => '201',
                'floor_id' => $secondFloorId,
                'capacity' => 2,
                'price' => 16000,
                'created_by' => $creatorId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ], ['number'], ['floor_id', 'capacity', 'price', 'created_by', 'updated_at']);
    }
}
