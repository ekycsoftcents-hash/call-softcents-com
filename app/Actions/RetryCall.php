<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\CallStatus;
use App\Enums\CallType;
use App\Enums\CampaignStatus;
use App\Enums\TransactionType;
use App\Exceptions\BusinessException;
use App\Models\Call;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Throwable;

final class RetryCall
{
    /**
     * Re-queue a failed call, reserving the estimated cost from the user's balance.
     *
     * Mirrors CreateNewCall/CreateNewOtpCall: locks the user row, validates balance,
     * creates a debit transaction, restores the cost on the call, resets stale
     * call fields from the previous attempt, and sets status back to Pending.
     *
     * @throws BusinessException
     * @throws Throwable
     */
    public function handle(Call $call): void
    {
        if (! $call->can_retry) {
            throw new BusinessException('Call cannot be retried.');
        }

        DB::transaction(function () use ($call): void {
            $lockedUser = User::query()
                ->whereKey($call->user_id)
                ->lockForUpdate()
                ->firstOrFail();

            $cost = $this->estimateCost($call, $lockedUser);

            if (! $lockedUser->hasEnoughBalance($cost)) {
                throw new BusinessException('Insufficient balance to retry this call.');
            }

            $before = (float) $lockedUser->balance;
            $retryNumber = $call->retries + 1;

            $lockedUser->decrement('balance', $cost);

            $call->transactions()->create([
                'user_id' => $lockedUser->id,
                'type' => TransactionType::Debit,
                'amount' => $cost,
                'balance_before' => $before,
                'balance_after' => $before - $cost,
                'currency' => 'BDT',
                'description' => "Reserved balance for call #{$call->id} retry #{$retryNumber}",
            ]);

            $call->update([
                'status' => CallStatus::Pending,
                'cost' => $cost,
                'duration' => 0,
                'hangup_cause' => null,
                'unique_id' => null,
                'poll_attempt' => null,
                'next_poll_at' => null,
            ]);

            $call->increment('retries');

            if ($call->campaign && in_array($call->campaign->status, [CampaignStatus::Finished, CampaignStatus::Failed])) {
                $call->campaign->update(['status' => CampaignStatus::Processing]);
            }
        });
    }

    /**
     * @throws BusinessException
     */
    private function estimateCost(Call $call, User $user): float
    {
        if ($call->type === CallType::Marketing) {
            $call->loadMissing('audio');

            if (! $call->audio) {
                throw new BusinessException('Audio not found for this call.');
            }

            $cost = $call->audio->cost;

            if ($cost <= 0) {
                throw new BusinessException('Audio cost could not be calculated. Check that audio duration and pulse rate are configured.');
            }

            return $cost;
        }

        // OTP: reserve a single pulse, same as CreateNewOtpCall
        $cost = (float) ($user->pulse_rate ?? 0);

        if ($cost <= 0) {
            throw new BusinessException('OTP cost is not configured for this user.');
        }

        return $cost;
    }
}
