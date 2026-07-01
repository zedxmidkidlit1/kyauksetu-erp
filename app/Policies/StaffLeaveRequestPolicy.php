<?php

namespace App\Policies;

use App\Models\StaffLeaveRequest;
use App\Models\User;

class StaffLeaveRequestPolicy
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
        return $user->can('staff_leave_requests.view');
    }

    public function view(User $user, StaffLeaveRequest $staffLeaveRequest): bool
    {
        return $user->can('staff_leave_requests.view');
    }

    public function create(User $user): bool
    {
        return $user->can('staff_leave_requests.create');
    }

    public function update(User $user, StaffLeaveRequest $staffLeaveRequest): bool
    {
        return $user->can('staff_leave_requests.update');
    }

    public function delete(User $user, StaffLeaveRequest $staffLeaveRequest): bool
    {
        return $user->can('staff_leave_requests.delete');
    }

    public function restore(User $user, StaffLeaveRequest $staffLeaveRequest): bool
    {
        return false;
    }

    public function forceDelete(User $user, StaffLeaveRequest $staffLeaveRequest): bool
    {
        return false;
    }
}
