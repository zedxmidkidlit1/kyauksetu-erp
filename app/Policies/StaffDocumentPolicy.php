<?php

namespace App\Policies;

use App\Models\StaffDocument;
use App\Models\User;

class StaffDocumentPolicy
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
        return $user->can('staff_documents.view');
    }

    public function view(User $user, StaffDocument $staffDocument): bool
    {
        return $user->can('staff_documents.view');
    }

    public function create(User $user): bool
    {
        return $user->can('staff_documents.create');
    }

    public function update(User $user, StaffDocument $staffDocument): bool
    {
        return $user->can('staff_documents.update');
    }

    public function delete(User $user, StaffDocument $staffDocument): bool
    {
        return $user->can('staff_documents.delete');
    }

    public function restore(User $user, StaffDocument $staffDocument): bool
    {
        return false;
    }

    public function forceDelete(User $user, StaffDocument $staffDocument): bool
    {
        return false;
    }
}
