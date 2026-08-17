<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\UserAudioType;
use App\Enums\UserStatus;
use App\Enums\UserType;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Guarded(['id'])]
final class User extends Authenticatable implements FilamentUser, HasAvatar
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $casts = [
        'type' => UserType::class,
        'audio_type' => UserAudioType::class,
        'status' => UserStatus::class,
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'balance' => 'float',
        'pulse_rate' => 'float',
        'pulse_duration' => 'integer',
        'rate' => 'float',
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

    public function reseller(): BelongsTo
    {
        return $this->belongsTo(self::class, 'reseller_id');
    }

    public function clients(): HasMany
    {
        return $this->hasMany(self::class, 'reseller_id');
    }

    public function branding(): HasOne
    {
        return $this->hasOne(ResellerBranding::class, 'reseller_id');
    }

    public function audio(): HasMany
    {
        return $this->hasMany(Audio::class);
    }

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }

    public function templates(): HasMany
    {
        return $this->hasMany(Template::class);
    }

    public function callers(): BelongsToMany
    {
        return $this->belongsToMany(Caller::class, 'caller_user');
    }

    public function deposits(): HasMany
    {
        return $this->hasMany(Deposit::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    public function calls(): HasMany
    {
        return $this->hasMany(Call::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // Managed by AdminMiddleware and UserMiddleware
        return true;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url;
    }

    public function isAdmin(): bool
    {
        return $this->type === UserType::Admin;
    }

    public function isReseller(): bool
    {
        return $this->type === UserType::Reseller;
    }

    public function isClient(): bool
    {
        return $this->type === UserType::User && $this->reseller_id !== null;
    }

    public function isUser(): bool
    {
        return $this->type === UserType::User;
    }

    public function hasEnoughBalance(float $amount): bool
    {
        return $this->balance >= $amount;
    }

    #[Scope]
    protected function admin(Builder $query): Builder
    {
        return $query->where('type', UserType::Admin);
    }

    #[Scope]
    protected function user(Builder $query): Builder
    {
        return $query->where('type', UserType::User);
    }
}
