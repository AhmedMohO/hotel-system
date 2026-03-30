<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
   use HasFactory;

    protected $fillable = [
        'client_id',
        'room_id',
        'checkin_date',
        'checkout_date',
        'accompany_number',
        'total_price',
        'paid_price',
        'payment_method',
        'status',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}


