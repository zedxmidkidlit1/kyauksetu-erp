<?php

namespace App\Policies;

use App\Models\AdmissionDecision;
use App\Models\User;

class AdmissionDecisionPolicy
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
        return $user->can('admission_decisions.view');
    }

    public function view(User $user, AdmissionDecision $admissionDecision): bool
    {
        return $user->can('admission_decisions.view');
    }

    public function create(User $user): bool
    {
        return $user->can('admission_decisions.create');
    }

    public function update(User $user, AdmissionDecision $admissionDecision): bool
    {
        return $user->can('admission_decisions.update');
    }

    public function delete(User $user, AdmissionDecision $admissionDecision): bool
    {
        return $user->can('admission_decisions.delete');
    }

    public function restore(User $user, AdmissionDecision $admissionDecision): bool
    {
        return false;
    }

    public function forceDelete(User $user, AdmissionDecision $admissionDecision): bool
    {
        return false;
    }
}
