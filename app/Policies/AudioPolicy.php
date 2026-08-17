<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Audio;
use App\Models\User;

final class AudioPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Audio $audio): bool
    {
        return $user->isAdmin() || $user->id === $audio->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Audio $audio): bool
    {
        return $user->isAdmin() || $user->id === $audio->user_id;
    }

    public function delete(User $user, Audio $audio): bool
    {
        return $user->isAdmin() || $user->id === $audio->user_id;
    }
}
