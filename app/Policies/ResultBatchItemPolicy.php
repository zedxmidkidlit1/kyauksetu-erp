<?php

namespace App\Policies;

use App\Models\ResultBatchItem;
use App\Models\User;

class ResultBatchItemPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('result_batch_items.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ResultBatchItem $resultBatchItem): bool
    {
        return $user->can('result_batch_items.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('result_batch_items.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ResultBatchItem $resultBatchItem): bool
    {
        return $user->can('result_batch_items.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ResultBatchItem $resultBatchItem): bool
    {
        return $user->can('result_batch_items.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ResultBatchItem $resultBatchItem): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ResultBatchItem $resultBatchItem): bool
    {
        return false;
    }
}
