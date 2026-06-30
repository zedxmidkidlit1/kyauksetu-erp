<?php

namespace App\Policies;

use App\Models\TimetableSlot;
use App\Models\User;

class TimetableSlotPolicy
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

    public function view(User $user, TimetableSlot $timetableSlot): bool
    {
        return $user->can('timetables.view');
    }

    public function create(User $user): bool
    {
        return $user->can('timetables.update');
    }

    public function update(User $user, TimetableSlot $timetableSlot): bool
    {
        return $user->can('timetables.update');
    }

    public function delete(User $user, TimetableSlot $timetableSlot): bool
    {
        return $user->can('timetables.update');
    }

    public function restore(User $user, TimetableSlot $timetableSlot): bool
    {
        return false;
    }

    public function forceDelete(User $user, TimetableSlot $timetableSlot): bool
    {
        return false;
    }
}
