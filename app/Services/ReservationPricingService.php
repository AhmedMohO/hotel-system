<?php

namespace App\Services;

use Carbon\CarbonImmutable;

class ReservationPricingService
{
    public function calculateNights(string $checkIn, string $checkOut): int
    {
        $start = CarbonImmutable::parse($checkIn)->startOfDay();
        $end = CarbonImmutable::parse($checkOut)->startOfDay();

        return $start->diffInDays($end);
    }

    public function calculatePaidPriceCents(int $roomPriceCents, string $checkIn, string $checkOut): int
    {
        return $roomPriceCents * $this->calculateNights($checkIn, $checkOut);
    }
}

