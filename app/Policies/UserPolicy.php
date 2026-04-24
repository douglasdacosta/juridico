<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function view(User $authUser, User $targetUser): bool
    {
        return $authUser->isAdmin() || $authUser->id === $targetUser->id;
    }

    public function update(User $authUser, User $targetUser): bool
    {
        return $authUser->isAdmin() || $authUser->id === $targetUser->id;
    }
}
