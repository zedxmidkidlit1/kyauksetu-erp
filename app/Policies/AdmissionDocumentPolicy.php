<?php

namespace App\Policies;

use App\Models\AdmissionDocument;
use App\Models\User;

class AdmissionDocumentPolicy
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
        return $user->can('admission_documents.view');
    }

    public function view(User $user, AdmissionDocument $admissionDocument): bool
    {
        return $user->can('admission_documents.view');
    }

    public function create(User $user): bool
    {
        return $user->can('admission_documents.create');
    }

    public function update(User $user, AdmissionDocument $admissionDocument): bool
    {
        return $user->can('admission_documents.update');
    }

    public function delete(User $user, AdmissionDocument $admissionDocument): bool
    {
        return $user->can('admission_documents.delete');
    }

    public function restore(User $user, AdmissionDocument $admissionDocument): bool
    {
        return false;
    }

    public function forceDelete(User $user, AdmissionDocument $admissionDocument): bool
    {
        return false;
    }
}
