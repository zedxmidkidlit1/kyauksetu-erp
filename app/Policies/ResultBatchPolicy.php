<?php

namespace App\Policies;

use App\Models\ResultBatch;
use App\Models\User;

class ResultBatchPolicy
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
        return $user->can('result_batches.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ResultBatch $resultBatch): bool
    {
        return $user->can('result_batches.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('result_batches.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ResultBatch $resultBatch): bool
    {
        return $user->can('result_batches.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ResultBatch $resultBatch): bool
    {
        return $user->can('result_batches.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ResultBatch $resultBatch): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ResultBatch $resultBatch): bool
    {
        return false;
    }
}
