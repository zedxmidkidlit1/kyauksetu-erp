<?php

namespace App\Policies;

use App\Models\StudentPayment;
use App\Models\User;

class StudentPaymentPolicy
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
        return $user->can('student_payments.view');
    }

    public function view(User $user, StudentPayment $studentPayment): bool
    {
        return $user->can('student_payments.view');
    }

    public function create(User $user): bool
    {
        return $user->can('student_payments.create');
    }

    public function update(User $user, StudentPayment $studentPayment): bool
    {
        return $user->can('student_payments.update');
    }

    public function delete(User $user, StudentPayment $studentPayment): bool
    {
        return $user->can('student_payments.delete');
    }

    public function restore(User $user, StudentPayment $studentPayment): bool
    {
        return false;
    }

    public function forceDelete(User $user, StudentPayment $studentPayment): bool
    {
        return false;
    }
}
