<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\Transactionable;
use App\Enums\CallStatus;
use App\Enums\CampaignApproval;
use App\Enums\CampaignStatus;
use App\Models\Scopes\OwnedByAuthUser;
use Database\Factories\CampaignFactory;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\DB;
use Throwable;

#[ScopedBy(OwnedByAuthUser::class)]
#[Guarded(['id'])]
final class Campaign extends Model implements Transactionable
{
    /** @use HasFactory<CampaignFactory> */
    use HasFactory;

    protected $casts = [
        'status' => CampaignStatus::class,
        'prev_status' => CampaignStatus::class,
        'approval' => CampaignApproval::class,
        'scheduled_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function audio(): BelongsTo
    {
        return $this->belongsTo(Audio::class);
    }

    public function caller(): BelongsTo
    {
        return $this->belongsTo(Caller::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function calls(): HasMany
    {
        return $this->hasMany(Call::class);
    }

    public function transactions(): MorphMany
    {
        return $this->morphMany(Transaction::class, 'transactionable');
    }

    /**
     * @throws Throwable
     */
    public function pause(): void
    {
        if (! $this->status->isPausable()) {
            return;
        }

        DB::transaction(function () {
            $this->update([
                'status' => CampaignStatus::Paused,
                'prev_status' => $this->status,
            ]);

            $this->calls()
                ->where('status', '!=', CallStatus::Paused)
                ->update([
                    'prev_status' => DB::raw('status'),
                    'status' => CallStatus::Paused,
                ]);
        });

    }

    /**
     * @throws Throwable
     */
    public function resume(): void
    {
        if (! $this->status->isPaused()) {
            return;
        }

        DB::transaction(function () {
            $this->update([
                'status' => $this->prev_status ?? CampaignStatus::Pending,
                'prev_status' => null,
            ]);

            $this->calls()
                ->where('status', CallStatus::Paused)
                ->whereNotNull('prev_status')
                ->update([
                    'status' => DB::raw('prev_status'),
                    'prev_status' => null,
                ]);
        });

    }

    #[Scope]
    protected function pending(Builder $query): Builder
    {
        return $query->whereStatus(CampaignStatus::Pending);
    }

    #[Scope]
    protected function processing(Builder $query): Builder
    {
        return $query->whereStatus(CampaignStatus::Processing);
    }

    /**
     * Scope a query to only include scheduled campaigns.
     */
    #[Scope]
    protected function scheduled(Builder $query): Builder
    {
        return $query->whereNotNull('scheduled_at');
    }

    #[Scope]
    protected function notScheduled(Builder $query): Builder
    {
        return $query->whereNull('scheduled_at');
    }

    #[Scope]
    protected function approved(Builder $query): Builder
    {
        return $query->where('approval', CampaignApproval::Approved);
    }
}
