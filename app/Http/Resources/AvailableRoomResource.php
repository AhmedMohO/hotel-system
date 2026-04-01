<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AvailableRoomResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'number' => $this->number,
            'capacity' => $this->capacity,
            'price' => (int) $this->price,
            'price_formatted' => number_format(((int) $this->price) / 100, 2),
            'floor' => $this->whenLoaded('floor', fn () => [
                'id' => $this->floor->id,
                'name' => $this->floor->name,
                'number' => $this->floor->number,
            ]),
        ];
    }
}

