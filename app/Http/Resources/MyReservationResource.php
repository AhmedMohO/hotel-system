<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MyReservationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'check_in' => optional($this->check_in)->format('Y-m-d'),
            'check_out' => optional($this->check_out)->format('Y-m-d'),
            'accompany_number' => $this->accompany_number,
            'paid_price' => (int) $this->paid_price,
            'paid_price_formatted' => number_format(((int) $this->paid_price) / 100, 2),
            'room' => [
                'id' => $this->room?->id,
                'number' => $this->room?->number,
            ],
        ];
    }
}

