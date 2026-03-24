<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cog\Contracts\Ban\Bannable as BannableContract;
use Cog\Laravel\Ban\Traits\Bannable;

#[Fillable(['name', 'email', 'password', 'national_id', 'avatar_image', 'created_by'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements BannableContract
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable, HasRoles, SoftDeletes, Bannable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    /** The manager who created this receptionist. */
    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    /** Receptionists created by this manager. */
    public function receptionists(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(User::class, 'created_by');
    }

    /** Check if user can be force deleted or must be soft deleted. */
    public function canBeHardDeleted(): bool
    {
        return !(\Illuminate\Support\Facades\DB::table('users')->where('created_by', $this->id)->exists()
            || \Illuminate\Support\Facades\DB::table('floors')->where('created_by', $this->id)->exists()
            || \Illuminate\Support\Facades\DB::table('rooms')->where('created_by', $this->id)->exists()
            || \Illuminate\Support\Facades\DB::table('clients')->where('approved_by', $this->id)->exists()
            || \Illuminate\Support\Facades\DB::table('reservations')->where('approved_by', $this->id)->exists()
        );
    }
}
