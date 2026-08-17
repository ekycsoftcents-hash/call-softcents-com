<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\CallInterface;
use App\Enums\CallStatus;
use App\Enums\CallType;
use App\Enums\TransactionType;
use App\Exceptions\BusinessException;
use App\Jobs\InitiateCallJob;
use App\Models\Call;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Throwable;

final class CreateNewOtpCall
{
    /**
     * Create a new OTP call and reserve its estimated cost up front.
     *
     * OTP playback (pre-otp + digits + post-otp) is short, so we reserve
     * a single pulse worth of cost. Reconciliation in PollCallCdrJob will
     * adjust against actual cdr->billsec.
     *
     * @throws Throwable
     */
    public function handle(User $user, array $input, CallInterface $interface = CallInterface::Web): Call
    {
        $call = DB::transaction(function () use ($user, $input, $interface): Call {
            $lockedUser = User::whereKey($user->id)
                ->lockForUpdate()
                ->firstOrFail();

            $cost = (float) ($lockedUser->pulse_rate ?? 0);

            if ($cost <= 0) {
                throw new BusinessException('OTP cost is not configured for this user');
            }

            if (! $lockedUser->hasEnoughBalance($cost)) {
                throw new BusinessException('Insufficient balance');
            }

            /** @var Call $call */
            $call = $user->calls()->create(array_merge($input, [
                'type' => CallType::OTP,
                'status' => CallStatus::Pending,
                'interface' => $interface,
                'cost' => $cost,
            ]));

            $before = (float) $lockedUser->balance;

            $lockedUser->decrement('balance', $cost);

            $call->transactions()->create([
                'user_id' => $lockedUser->id,
                'type' => TransactionType::Debit,
                'amount' => $cost,
                'balance_before' => $before,
                'balance_after' => $before - $cost,
                'currency' => 'BDT',
                'description' => "Reserved balance for OTP call #{$call->id}",
            ]);

            return $call;
        });

        if (! $call->scheduled_at || $call->scheduled_at->isPast()) {
            InitiateCallJob::dispatch($call->id)->onQueue('calling');
        }

        return $call;
    }
}
