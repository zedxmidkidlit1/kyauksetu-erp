<?php

namespace App\Policies;

use App\Models\StudentFee;
use App\Models\User;

class StudentFeePolicy
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
        return $user->can('student_fees.view');
    }

    public function view(User $user, StudentFee $studentFee): bool
    {
        return $user->can('student_fees.view');
    }

    public function create(User $user): bool
    {
        return $user->can('student_fees.create');
    }

    public function update(User $user, StudentFee $studentFee): bool
    {
        return $user->can('student_fees.update');
    }

    public function delete(User $user, StudentFee $studentFee): bool
    {
        return $user->can('student_fees.delete');
    }

    public function restore(User $user, StudentFee $studentFee): bool
    {
        return false;
    }

    public function forceDelete(User $user, StudentFee $studentFee): bool
    {
        return false;
    }
}
