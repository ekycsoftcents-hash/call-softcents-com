<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Template;
use App\Models\User;

final class TemplatePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Template $template): bool
    {
        return $user->isAdmin() || $user->id === $template->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Template $template): bool
    {
        return $user->isAdmin() || $user->id === $template->user_id;
    }

    public function delete(User $user, Template $template): bool
    {
        return $user->isAdmin() || $user->id === $template->user_id;
    }
}
