<?php

namespace App\Models;

use App\Notifications\ClientResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Client extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_image',
        'mobile',
        'country',
        'gender',
        'approved_by',
        'approved_at',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'last_login_at' => 'datetime',
        'password' => 'hashed',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    /**
     * The staff member (user) who approved this client.
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Reservations belonging to this client.
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    /**
     * Scope: only unapproved clients.
     */
    public function scopeUnapproved($query)
    {
        return $query->whereNull('approved_at');
    }

    /**
     * Scope: only approved clients.
     */
    public function scopeApproved($query)
    {
        return $query->whereNotNull('approved_at');
    }

    /**
     * Scope: clients approved by a specific receptionist.
     */
    public function scopeApprovedBy($query, int $userId)
    {
        return $query->where('approved_by', $userId)->whereNotNull('approved_at');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function isApproved(): bool
    {
        return ! is_null($this->approved_at);
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar_image
            ? asset('storage/'.$this->avatar_image)
            : asset('images/default-avatar.png');
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ClientResetPasswordNotification($token));
    }
}
