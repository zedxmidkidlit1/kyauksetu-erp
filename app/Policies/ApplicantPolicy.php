<?php

namespace App\Policies;

use App\Models\Applicant;
use App\Models\User;

class ApplicantPolicy
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
        return $user->can('applicants.view');
    }

    public function view(User $user, Applicant $applicant): bool
    {
        return $user->can('applicants.view');
    }

    public function create(User $user): bool
    {
        return $user->can('applicants.create');
    }

    public function update(User $user, Applicant $applicant): bool
    {
        return $user->can('applicants.update');
    }

    public function delete(User $user, Applicant $applicant): bool
    {
        return $user->can('applicants.delete');
    }

    public function restore(User $user, Applicant $applicant): bool
    {
        return false;
    }

    public function forceDelete(User $user, Applicant $applicant): bool
    {
        return false;
    }
}
