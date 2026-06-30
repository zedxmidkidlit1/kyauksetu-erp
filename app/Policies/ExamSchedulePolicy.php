<?php

namespace App\Policies;

use App\Models\ExamSchedule;
use App\Models\User;

class ExamSchedulePolicy
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
        return $user->can('exam_schedules.view');
    }

    public function view(User $user, ExamSchedule $examSchedule): bool
    {
        return $user->can('exam_schedules.view');
    }

    public function create(User $user): bool
    {
        return $user->can('exam_schedules.create');
    }

    public function update(User $user, ExamSchedule $examSchedule): bool
    {
        return $user->can('exam_schedules.update');
    }

    public function delete(User $user, ExamSchedule $examSchedule): bool
    {
        return $user->can('exam_schedules.delete');
    }

    public function restore(User $user, ExamSchedule $examSchedule): bool
    {
        return false;
    }

    public function forceDelete(User $user, ExamSchedule $examSchedule): bool
    {
        return false;
    }
}
