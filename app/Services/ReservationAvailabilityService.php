<?php

namespace App\Services;

use App\Models\Room;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ReservationAvailabilityService
{
    public function availableRoomsQuery(string $checkIn, string $checkOut, int $accompanyNumber): Builder
    {
        return Room::query()
            ->with('floor:id,name,number')
            ->available($checkIn, $checkOut)
            ->where('capacity', '>=', $accompanyNumber)
            ->orderBy('number');
    }

    public function availableRooms(string $checkIn, string $checkOut, int $accompanyNumber): Collection
    {
        return $this->availableRoomsQuery($checkIn, $checkOut, $accompanyNumber)->get();
    }

    public function roomIsAvailable(int $roomId, string $checkIn, string $checkOut): bool
    {
        return Room::query()
            ->whereKey($roomId)
            ->available($checkIn, $checkOut)
            ->exists();
    }
}


