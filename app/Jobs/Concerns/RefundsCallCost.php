<?php

declare(strict_types=1);

namespace App\Jobs\Concerns;

use App\Enums\CallStatus;
use App\Enums\TransactionType;
use App\Models\Call;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

trait RefundsCallCost
{
    /**
     * Refund any reserved cost on the call and mark it as failed.
     *
     * Idempotent: safe to call multiple times. Locks the call and user rows
     * so concurrent reconciliation (e.g. PollCallCdrJob) cannot double-refund.
     *
     * @throws Throwable
     */
    protected function refundCallCost(int $callId, string $reason): void
    {
        DB::transaction(function () use ($callId, $reason): void {
            $call = Call::query()
                ->withoutGlobalScopes()
                ->whereKey($callId)
                ->lockForUpdate()
                ->first();

            if (! $call) {
                Log::warning('Refund skipped: call not found', ['call_id' => $callId]);

                return;
            }

            // Idempotency guard — already settled (Failed with cost=0, or Completed).
            if ($call->status === CallStatus::Completed) {
                return;
            }

            $cost = (float) $call->cost;

            if ($cost > 0) {
                $user = User::query()
                    ->whereKey($call->user_id)
                    ->lockForUpdate()
                    ->first();

                if ($user) {
                    $before = (float) $user->balance;

                    $user->increment('balance', $cost);

                    $call->transactions()->create([
                        'user_id' => $user->id,
                        'type' => TransactionType::Credit,
                        'amount' => $cost,
                        'balance_before' => $before,
                        'balance_after' => $before + $cost,
                        'currency' => 'BDT',
                        'description' => "Refund for call #{$call->id}: {$reason}",
                    ]);
                } else {
                    Log::error('Refund failed: user not found', [
                        'call_id' => $call->id,
                        'user_id' => $call->user_id,
                    ]);
                }
            }

            $call->update([
                'status' => CallStatus::Failed,
                'cost' => 0,
            ]);
        });
    }
}
