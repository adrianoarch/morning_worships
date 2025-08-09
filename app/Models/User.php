<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\VerifyEmailQueued;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\MorningWorship;
use App\Models\UserWatchedWorship;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'receives_email_notification',
        'phone',
        'timezone',
        'language',
    ];

    /**
     * Send the email verification notification via queue.
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify((new VerifyEmailQueued())->onQueue('mail'));
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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
        ];
    }

    /**
     * Adoracoes matinais assistidas pelo usuario.
     *
     * @return BelongsToMany<MorningWorship, User>
     */
    public function watchedWorships(): BelongsToMany
    {
        return $this->belongsToMany(MorningWorship::class, 'user_watched_worships')
            ->withTimestamps()
            ->withPivot('watched_at', 'notes');
    }
}
