<?php

namespace App\Policies;

use App\Models\Asset;
use App\Models\User;

class AssetPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('assets.view');
    }

    public function view(User $user, Asset $asset): bool
    {
        return $user->can('assets.view');
    }

    public function create(User $user): bool
    {
        return $user->can('assets.create');
    }

    public function update(User $user, Asset $asset): bool
    {
        return $user->can('assets.update');
    }

    public function delete(User $user, Asset $asset): bool
    {
        return $user->can('assets.delete');
    }

    public function restore(User $user, Asset $asset): bool
    {
        return false;
    }

    public function forceDelete(User $user, Asset $asset): bool
    {
        return false;
    }
}
