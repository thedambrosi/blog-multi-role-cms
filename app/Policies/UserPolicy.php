<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the admin can remove the target user's access.
     */
    public function removeAccess(User $admin, User $target): bool
    {
        return $admin->role === 'admin' && $admin->id !== $target->id;
    }
}
