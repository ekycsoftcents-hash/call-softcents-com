<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Contact;
use App\Models\User;

final class ContactPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Contact $contact): bool
    {
        return $user->isAdmin() || $user->id === $contact->group->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Contact $contact): bool
    {
        return $user->isAdmin() || $user->id === $contact->group->user_id;
    }

    public function delete(User $user, Contact $contact): bool
    {
        return $user->isAdmin() || $user->id === $contact->group->user_id;
    }
}
