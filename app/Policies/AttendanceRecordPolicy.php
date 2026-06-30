<?php

namespace App\Policies;

use App\Models\AttendanceRecord;
use App\Models\User;

class AttendanceRecordPolicy
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
        return $user->can('attendance_records.view');
    }

    public function view(User $user, AttendanceRecord $attendanceRecord): bool
    {
        return $user->can('attendance_records.view');
    }

    public function create(User $user): bool
    {
        return $user->can('attendance_records.create');
    }

    public function update(User $user, AttendanceRecord $attendanceRecord): bool
    {
        return $user->can('attendance_records.update');
    }

    public function delete(User $user, AttendanceRecord $attendanceRecord): bool
    {
        return $user->can('attendance_records.delete');
    }

    public function restore(User $user, AttendanceRecord $attendanceRecord): bool
    {
        return false;
    }

    public function forceDelete(User $user, AttendanceRecord $attendanceRecord): bool
    {
        return false;
    }
}
