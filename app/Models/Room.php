<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    public function floor(): BelongsTo
    {
        return $this->belongsTo(Floor::class);
    }
}
