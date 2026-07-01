<?php

namespace App\Policies;

use App\Models\AssetCategory;
use App\Models\User;

class AssetCategoryPolicy
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
        return $user->can('asset_categories.view');
    }

    public function view(User $user, AssetCategory $assetCategory): bool
    {
        return $user->can('asset_categories.view');
    }

    public function create(User $user): bool
    {
        return $user->can('asset_categories.create');
    }

    public function update(User $user, AssetCategory $assetCategory): bool
    {
        return $user->can('asset_categories.update');
    }

    public function delete(User $user, AssetCategory $assetCategory): bool
    {
        return $user->can('asset_categories.delete');
    }

    public function restore(User $user, AssetCategory $assetCategory): bool
    {
        return false;
    }

    public function forceDelete(User $user, AssetCategory $assetCategory): bool
    {
        return false;
    }
}
