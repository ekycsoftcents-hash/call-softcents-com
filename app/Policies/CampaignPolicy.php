<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Campaign;
use App\Models\User;

final class CampaignPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Campaign $campaign): bool
    {
        return $user->isAdmin() || $user->id === $campaign->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Campaign $campaign): bool
    {
        if (! $user->isAdmin() && $user->id !== $campaign->user_id) {
            return false;
        }

        if ($campaign->scheduled_at === null) {
            return false;
        }

        return ! $campaign->scheduled_at->isPast();
    }

    public function delete(User $user, Campaign $campaign): bool
    {
        return $user->isAdmin() || $user->id === $campaign->user_id;
    }
}
