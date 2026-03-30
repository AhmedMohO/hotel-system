<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['number', 'floor_id', 'capacity', 'price', 'created_by'];

    public function floor(): BelongsTo
    {
        return $this->belongsTo(Floor::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function scopeAvailable(Builder $query, string $checkIn, string $checkOut): Builder
    {
        return $query->whereDoesntHave('reservations', function (Builder $reservationQuery) use ($checkIn, $checkOut) {
            $reservationQuery
                ->where('check_in', '<', $checkOut)
                ->where('check_out', '>', $checkIn);
        });
    }
}
