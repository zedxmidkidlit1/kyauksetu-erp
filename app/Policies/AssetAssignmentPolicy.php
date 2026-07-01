<?php

namespace App\Policies;

use App\Models\AssetAssignment;
use App\Models\User;

class AssetAssignmentPolicy
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
        return $user->can('asset_assignments.view');
    }

    public function view(User $user, AssetAssignment $assetAssignment): bool
    {
        return $user->can('asset_assignments.view');
    }

    public function create(User $user): bool
    {
        return $user->can('asset_assignments.create');
    }

    public function update(User $user, AssetAssignment $assetAssignment): bool
    {
        return $user->can('asset_assignments.update');
    }

    public function delete(User $user, AssetAssignment $assetAssignment): bool
    {
        return $user->can('asset_assignments.delete');
    }

    public function restore(User $user, AssetAssignment $assetAssignment): bool
    {
        return false;
    }

    public function forceDelete(User $user, AssetAssignment $assetAssignment): bool
    {
        return false;
    }
}
