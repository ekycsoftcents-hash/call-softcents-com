<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CallerFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Caller extends Model
{
    /** @use HasFactory<CallerFactory> */
    use HasFactory;

    protected $fillable = [
        'server_id',
        'caller_name',
        'caller_number',
        'sip_username',
        'sip_password',
        'sip_domain',
        'sip_port',
        'sip_context',
        'enabled',
        'max_concurrency',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'sip_password' => 'encrypted',
        'sip_port' => 'integer',
        'max_concurrency' => 'integer',
    ];

    protected $appends = ['name'];

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'caller_user');
    }

    public function calls(): HasMany
    {
        return $this->hasMany(Call::class);
    }

    /**
     * Get the count of active calls for this caller.
     */
    public function activeCallsCount(): int
    {
        return Call::active()
            ->whereCallerId($this->id)
            ->count();
    }

    public function availableSlots(): int
    {
        return max(0, $this->max_concurrency - $this->activeCallsCount());
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->caller_name.' <'.$this->caller_number.'>',
        );
    }

    #[Scope]
    protected function enabled(Builder $query): Builder
    {
        return $query->where('enabled', true);
    }

    #[Scope]
    protected function online(Builder $query): Builder
    {
        return $query->where('is_online', true);
    }

    #[Scope]
    protected function syncable(Builder $query): Builder
    {
        return $query->enabled()->where(function (Builder $query) {
            $query->whereNull('last_synced_at')
                ->orWhere('last_synced_at', '<', now()->subMinutes(5));
        });
    }
}
