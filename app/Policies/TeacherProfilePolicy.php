<?php

namespace App\Policies;

use App\Models\TeacherProfile;
use App\Models\User;

class TeacherProfilePolicy
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
        return $user->can('teachers.view');
    }

    public function view(User $user, TeacherProfile $teacherProfile): bool
    {
        return $user->can('teachers.view');
    }

    public function create(User $user): bool
    {
        return $user->can('teachers.create');
    }

    public function update(User $user, TeacherProfile $teacherProfile): bool
    {
        return $user->can('teachers.update');
    }

    public function delete(User $user, TeacherProfile $teacherProfile): bool
    {
        return false;
    }

    public function restore(User $user, TeacherProfile $teacherProfile): bool
    {
        return false;
    }

    public function forceDelete(User $user, TeacherProfile $teacherProfile): bool
    {
        return false;
    }
}
