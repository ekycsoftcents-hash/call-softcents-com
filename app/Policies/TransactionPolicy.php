<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;

final class TransactionPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Transaction $transaction): bool
    {
        return $user->isAdmin() || $user->id === $transaction->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Transaction $transaction): bool
    {
        return $user->isAdmin() || $user->id === $transaction->user_id;
    }

    public function delete(User $user, Transaction $transaction): bool
    {
        return $user->isAdmin() || $user->id === $transaction->user_id;
    }
}
