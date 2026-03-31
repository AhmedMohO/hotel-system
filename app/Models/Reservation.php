<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'room_id',
        'check_in',
        'check_out',
        'accompany_number',
        'paid_price',
        'approved_by',
    ];

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'accompany_number' => 'integer',
        'paid_price' => 'integer',
        'approved_by' => 'integer',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}


