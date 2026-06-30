<?php

namespace App\Policies;

use App\Models\Timetable;
use App\Models\User;

class TimetablePolicy
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
        return $user->can('timetables.view');
    }

    public function view(User $user, Timetable $timetable): bool
    {
        return $user->can('timetables.view');
    }

    public function create(User $user): bool
    {
        return $user->can('timetables.create');
    }

    public function update(User $user, Timetable $timetable): bool
    {
        return $user->can('timetables.update');
    }

    public function delete(User $user, Timetable $timetable): bool
    {
        return $user->can('timetables.delete');
    }

    public function restore(User $user, Timetable $timetable): bool
    {
        return false;
    }

    public function forceDelete(User $user, Timetable $timetable): bool
    {
        return false;
    }
}
