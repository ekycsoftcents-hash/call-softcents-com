<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\TTSArtist;
use App\Models\User;

final class TTSArtistPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, TTSArtist $tTSArtist): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, TTSArtist $tTSArtist): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, TTSArtist $tTSArtist): bool
    {
        return $user->isAdmin();
    }
}
