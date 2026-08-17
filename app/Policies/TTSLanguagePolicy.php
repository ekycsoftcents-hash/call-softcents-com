<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\TTSLanguage;
use App\Models\User;

final class TTSLanguagePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, TTSLanguage $tTSLanguage): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, TTSLanguage $tTSLanguage): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, TTSLanguage $tTSLanguage): bool
    {
        return $user->isAdmin();
    }
}
