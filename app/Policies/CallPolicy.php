<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Call;
use App\Models\User;

final class CallPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Call $call): bool
    {
        return $user->isAdmin() || $user->id === $call->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Call $call): bool
    {
        return $user->isAdmin() || $user->id === $call->user_id;
    }

    public function delete(User $user, Call $call): bool
    {
        return $user->isAdmin() || $user->id === $call->user_id;
    }
}
