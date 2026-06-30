<?php

namespace App\Policies;

use App\Models\Curriculum;
use App\Models\User;

class CurriculumPolicy
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
        return $user->can('curriculums.view');
    }

    public function view(User $user, Curriculum $curriculum): bool
    {
        return $user->can('curriculums.view');
    }

    public function create(User $user): bool
    {
        return $user->can('curriculums.create');
    }

    public function update(User $user, Curriculum $curriculum): bool
    {
        return $user->can('curriculums.update');
    }

    public function delete(User $user, Curriculum $curriculum): bool
    {
        return $user->can('curriculums.delete');
    }

    public function restore(User $user, Curriculum $curriculum): bool
    {
        return false;
    }

    public function forceDelete(User $user, Curriculum $curriculum): bool
    {
        return false;
    }
}
