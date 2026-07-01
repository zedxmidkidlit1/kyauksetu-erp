<?php

namespace App\Policies;

use App\Models\StockItem;
use App\Models\User;

class StockItemPolicy
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
        return $user->can('stock_items.view');
    }

    public function view(User $user, StockItem $stockItem): bool
    {
        return $user->can('stock_items.view');
    }

    public function create(User $user): bool
    {
        return $user->can('stock_items.create');
    }

    public function update(User $user, StockItem $stockItem): bool
    {
        return $user->can('stock_items.update');
    }

    public function delete(User $user, StockItem $stockItem): bool
    {
        return $user->can('stock_items.delete');
    }

    public function restore(User $user, StockItem $stockItem): bool
    {
        return false;
    }

    public function forceDelete(User $user, StockItem $stockItem): bool
    {
        return false;
    }
}
