<?php

namespace App\Policies;

use App\Models\AttendanceSession;
use App\Models\User;

class AttendanceSessionPolicy
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
        return $user->can('attendance_sessions.view');
    }

    public function view(User $user, AttendanceSession $attendanceSession): bool
    {
        return $user->can('attendance_sessions.view');
    }

    public function create(User $user): bool
    {
        return $user->can('attendance_sessions.create');
    }

    public function update(User $user, AttendanceSession $attendanceSession): bool
    {
        return $user->can('attendance_sessions.update');
    }

    public function delete(User $user, AttendanceSession $attendanceSession): bool
    {
        return $user->can('attendance_sessions.delete');
    }

    public function restore(User $user, AttendanceSession $attendanceSession): bool
    {
        return false;
    }

    public function forceDelete(User $user, AttendanceSession $attendanceSession): bool
    {
        return false;
    }
}
