<?php

namespace App\Policies;

use App\Models\AdmissionApplication;
use App\Models\User;

class AdmissionApplicationPolicy
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
        return $user->can('admission_applications.view');
    }

    public function view(User $user, AdmissionApplication $admissionApplication): bool
    {
        return $user->can('admission_applications.view');
    }

    public function create(User $user): bool
    {
        return $user->can('admission_applications.create');
    }

    public function update(User $user, AdmissionApplication $admissionApplication): bool
    {
        return $user->can('admission_applications.update');
    }

    public function delete(User $user, AdmissionApplication $admissionApplication): bool
    {
        return $user->can('admission_applications.delete');
    }

    public function restore(User $user, AdmissionApplication $admissionApplication): bool
    {
        return false;
    }

    public function forceDelete(User $user, AdmissionApplication $admissionApplication): bool
    {
        return false;
    }
}
