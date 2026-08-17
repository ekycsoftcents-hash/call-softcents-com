<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Group;
use App\Models\User;

final class GroupPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Group $group): bool
    {
        return $user->isAdmin() || $user->id === $group->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Group $group): bool
    {
        return $user->isAdmin() || $user->id === $group->user_id;
    }

    public function delete(User $user, Group $group): bool
    {
        return $user->isAdmin() || $user->id === $group->user_id;
    }
}
