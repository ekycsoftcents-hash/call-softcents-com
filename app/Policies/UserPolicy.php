<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User as UserModel;

final class UserPolicy
{
    public function viewAny(UserModel $user): bool
    {
        return $user->isAdmin();
    }

    public function view(UserModel $user, UserModel $model): bool
    {
        return $user->isAdmin() || $user->id === $model->id;
    }

    public function create(UserModel $user): bool
    {
        return $user->isAdmin();
    }

    public function update(UserModel $user, UserModel $model): bool
    {
        return $user->isAdmin() || $user->id === $model->id;
    }

    public function delete(UserModel $user, UserModel $model): bool
    {
        return $user->isAdmin();
    }
}
