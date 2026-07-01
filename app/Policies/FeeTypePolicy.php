<?php

namespace App\Policies;

use App\Models\FeeType;
use App\Models\User;

class FeeTypePolicy
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
        return $user->can('fee_types.view');
    }

    public function view(User $user, FeeType $feeType): bool
    {
        return $user->can('fee_types.view');
    }

    public function create(User $user): bool
    {
        return $user->can('fee_types.create');
    }

    public function update(User $user, FeeType $feeType): bool
    {
        return $user->can('fee_types.update');
    }

    public function delete(User $user, FeeType $feeType): bool
    {
        return $user->can('fee_types.delete');
    }

    public function restore(User $user, FeeType $feeType): bool
    {
        return false;
    }

    public function forceDelete(User $user, FeeType $feeType): bool
    {
        return false;
    }
}
