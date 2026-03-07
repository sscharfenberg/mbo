<?php

namespace App\Models;

use App\Notifications\PasswordResetLinkNotification;
use App\Notifications\VerifyEmailNotification;
use App\Enums\Locale;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\Features;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable implements MustVerifyEmail, HasLocalePreference
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasUuids, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'locale',
        'email_verified_at',
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
            'password'          => 'hashed',
            'locale'            => Locale::class,
        ];
    }

    /**
     * Get the user's preferred locale for the HasLocalePreference interface.
     */
    public function preferredLocale(): string
    {
        return $this->locale->value;
    }

    /**
     * Send the email verification notification, if the feature is enabled.
     */
    public function sendEmailVerificationNotification(): void
    {
        if (!Features::enabled(Features::emailVerification())) {
            return;
        }

        $this->notify(new VerifyEmailNotification);
    }

    /**
     * Send the password reset notification.
     *
     * @param string $token
     */
    public function sendPasswordResetNotification(#[\SensitiveParameter] $token): void
    {
        $this->notify(new PasswordResetLinkNotification($token));
    }

    /**
     * Get the binders belonging to this user.
     *
     * @return HasMany<Binder>
     */
    public function binders(): HasMany
    {
        return $this->hasMany(Binder::class);
    }
}
