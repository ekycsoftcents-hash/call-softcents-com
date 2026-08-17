<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Caller;
use App\Models\User;

final class CallerPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Caller $caller): bool
    {
        return $user->isAdmin() || $caller->users()->where('user_id', $user->id)->exists();
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Caller $caller): bool
    {
        return $user->isAdmin() || $caller->users()->where('user_id', $user->id)->exists();
    }

    public function delete(User $user, Caller $caller): bool
    {
        return $user->isAdmin() || $caller->users()->where('user_id', $user->id)->exists();
    }
}
