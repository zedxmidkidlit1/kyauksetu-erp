<?php

namespace App\Policies;

use App\Models\LibraryLoan;
use App\Models\User;

class LibraryLoanPolicy
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
        return $user->can('library_loans.view');
    }

    public function view(User $user, LibraryLoan $libraryLoan): bool
    {
        return $user->can('library_loans.view');
    }

    public function create(User $user): bool
    {
        return $user->can('library_loans.create');
    }

    public function update(User $user, LibraryLoan $libraryLoan): bool
    {
        return $user->can('library_loans.update');
    }

    public function delete(User $user, LibraryLoan $libraryLoan): bool
    {
        return $user->can('library_loans.delete');
    }

    public function restore(User $user, LibraryLoan $libraryLoan): bool
    {
        return false;
    }

    public function forceDelete(User $user, LibraryLoan $libraryLoan): bool
    {
        return false;
    }
}
