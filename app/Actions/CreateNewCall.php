<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\CallInterface;
use App\Enums\TransactionType;
use App\Exceptions\BusinessException;
use App\Models\Call;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Throwable;

final class CreateNewCall
{
    /**
     * Create a new call and reserve its estimated cost.
     *
     * @throws Throwable
     */
    public function handle(User $user, array $input, CallInterface $interface = CallInterface::Web): Call
    {
        return DB::transaction(function () use ($user, $input, $interface): Call {
            /** @var Call $call */
            $call = $user->calls()->create(array_merge($input, [
                'interface' => $interface,
            ]));

            $lockedUser = User::whereKey($user->id)
                ->lockForUpdate()
                ->firstOrFail();

            $call->loadMissing('audio');

            if (! $call->audio) {
                throw new BusinessException('Audio not found');
            }

            $cost = $call->audio->cost;

            if (! $lockedUser->hasEnoughBalance($cost)) {
                throw new BusinessException('Insufficient balance');
            }

            $lockedUser->decrement('balance', $cost);

            $call->transactions()->create([
                'user_id' => $lockedUser->id,
                'type' => TransactionType::Debit,
                'amount' => $cost,
                'balance_before' => $lockedUser->balance + $cost,
                'balance_after' => $lockedUser->balance,
                'currency' => 'BDT',
                'description' => "Reserved balance for call $call->id",
            ]);

            $call->update(['cost' => $cost]);

            return $call->refresh();
        });
    }
}
