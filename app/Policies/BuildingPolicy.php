<?php

namespace App\Policies;

use App\Models\Building;
use App\Models\User;

class BuildingPolicy
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
        return $user->can('buildings.view');
    }

    public function view(User $user, Building $building): bool
    {
        return $user->can('buildings.view');
    }

    public function create(User $user): bool
    {
        return $user->can('buildings.create');
    }

    public function update(User $user, Building $building): bool
    {
        return $user->can('buildings.update');
    }

    public function delete(User $user, Building $building): bool
    {
        return $user->can('buildings.delete');
    }

    public function restore(User $user, Building $building): bool
    {
        return false;
    }

    public function forceDelete(User $user, Building $building): bool
    {
        return false;
    }
}
