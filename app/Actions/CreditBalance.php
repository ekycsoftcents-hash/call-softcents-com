<?php

declare(strict_types=1);

namespace App\Actions;

use App\Contracts\Transactionable;
use App\Enums\TransactionType;
use App\Models\Transaction;
use App\Models\User;
use InvalidArgumentException;

final readonly class CreditBalance
{
    public function handle(
        User $user,
        float|int $amount,
        Transactionable $transactionable,
        ?string $description = null,
    ): Transaction {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Credit amount must be greater than zero.');
        }

        // Caller must wrap this in a transaction with appropriate locks.
        $lockedUser = User::query()
            ->whereKey($user->id)
            ->lockForUpdate()
            ->firstOrFail();

        $balanceBefore = $lockedUser->balance;
        $balanceAfter = $balanceBefore + $amount;

        $lockedUser->update(['balance' => $balanceAfter]);

        return $transactionable->transactions()->create([
            'user_id' => $lockedUser->id,
            'amount' => $amount,
            'type' => TransactionType::Credit,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'description' => $description ?? 'Balance credited',
        ]);
    }
}
