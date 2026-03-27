<?php

namespace App\Models;

use App\Notifications\ClientResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword;

class Client extends Authenticatable
{
    use HasFactory, Notifiable, CanResetPassword;

    protected $guard = 'client';

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

    protected function casts(): array
    {
        return [
            'approved_at'  => 'datetime',
            'last_login_at' => 'datetime',
            'password'     => 'hashed',
        ];
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
