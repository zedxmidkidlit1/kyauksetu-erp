<?php

namespace App\Policies;

use App\Models\StudentStatusHistory;
use App\Models\User;

class StudentStatusHistoryPolicy
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
        return $user->can('student_status_histories.view');
    }

    public function view(User $user, StudentStatusHistory $studentStatusHistory): bool
    {
        return $user->can('student_status_histories.view');
    }

    public function create(User $user): bool
    {
        return $user->can('student_status_histories.create');
    }

    public function update(User $user, StudentStatusHistory $studentStatusHistory): bool
    {
        return $user->can('student_status_histories.update');
    }

    public function delete(User $user, StudentStatusHistory $studentStatusHistory): bool
    {
        return $user->can('student_status_histories.delete');
    }

    public function restore(User $user, StudentStatusHistory $studentStatusHistory): bool
    {
        return false;
    }

    public function forceDelete(User $user, StudentStatusHistory $studentStatusHistory): bool
    {
        return false;
    }
}
