<?php

namespace App\Policies;

use App\Models\ClassSection;
use App\Models\User;

class ClassSectionPolicy
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
        return $user->can('class_sections.view');
    }

    public function view(User $user, ClassSection $classSection): bool
    {
        return $user->can('class_sections.view');
    }

    public function create(User $user): bool
    {
        return $user->can('class_sections.create');
    }

    public function update(User $user, ClassSection $classSection): bool
    {
        return $user->can('class_sections.update');
    }

    public function delete(User $user, ClassSection $classSection): bool
    {
        return $user->can('class_sections.delete');
    }

    public function restore(User $user, ClassSection $classSection): bool
    {
        return false;
    }

    public function forceDelete(User $user, ClassSection $classSection): bool
    {
        return false;
    }
}
