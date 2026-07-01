<?php

namespace App\Policies;

use App\Models\StockMovement;
use App\Models\User;

class StockMovementPolicy
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
        return $user->can('stock_movements.view');
    }

    public function view(User $user, StockMovement $stockMovement): bool
    {
        return $user->can('stock_movements.view');
    }

    public function create(User $user): bool
    {
        return $user->can('stock_movements.create');
    }

    public function update(User $user, StockMovement $stockMovement): bool
    {
        return $user->can('stock_movements.update');
    }

    public function delete(User $user, StockMovement $stockMovement): bool
    {
        return $user->can('stock_movements.delete');
    }

    public function restore(User $user, StockMovement $stockMovement): bool
    {
        return false;
    }

    public function forceDelete(User $user, StockMovement $stockMovement): bool
    {
        return false;
    }
}
