<?php

namespace App\Policies;

use App\Models\User;

class BaseAccessPolicy
{
    public function check(User $user, string $permission): bool
    {
        return $user->canAccess($permission);
    }
}
