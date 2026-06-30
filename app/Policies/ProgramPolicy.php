<?php

namespace App\Policies;

use App\Models\Program;
use App\Models\User;

class ProgramPolicy
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
        return $user->can('programs.view');
    }

    public function view(User $user, Program $program): bool
    {
        return $user->can('programs.view');
    }

    public function create(User $user): bool
    {
        return $user->can('programs.create');
    }

    public function update(User $user, Program $program): bool
    {
        return $user->can('programs.update');
    }

    public function delete(User $user, Program $program): bool
    {
        return $user->can('programs.delete');
    }

    public function restore(User $user, Program $program): bool
    {
        return false;
    }

    public function forceDelete(User $user, Program $program): bool
    {
        return false;
    }
}
