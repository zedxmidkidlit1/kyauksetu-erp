<?php

namespace App\Policies;

use App\Models\AdmissionBatch;
use App\Models\User;

class AdmissionBatchPolicy
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
        return $user->can('admission_batches.view');
    }

    public function view(User $user, AdmissionBatch $admissionBatch): bool
    {
        return $user->can('admission_batches.view');
    }

    public function create(User $user): bool
    {
        return $user->can('admission_batches.create');
    }

    public function update(User $user, AdmissionBatch $admissionBatch): bool
    {
        return $user->can('admission_batches.update');
    }

    public function delete(User $user, AdmissionBatch $admissionBatch): bool
    {
        return $user->can('admission_batches.delete');
    }

    public function restore(User $user, AdmissionBatch $admissionBatch): bool
    {
        return false;
    }

    public function forceDelete(User $user, AdmissionBatch $admissionBatch): bool
    {
        return false;
    }
}
