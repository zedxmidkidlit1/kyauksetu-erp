<?php

namespace App\Policies;

use App\Models\BookCopy;
use App\Models\User;

class BookCopyPolicy
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
        return $user->can('book_copies.view');
    }

    public function view(User $user, BookCopy $bookCopy): bool
    {
        return $user->can('book_copies.view');
    }

    public function create(User $user): bool
    {
        return $user->can('book_copies.create');
    }

    public function update(User $user, BookCopy $bookCopy): bool
    {
        return $user->can('book_copies.update');
    }

    public function delete(User $user, BookCopy $bookCopy): bool
    {
        return $user->can('book_copies.delete');
    }

    public function restore(User $user, BookCopy $bookCopy): bool
    {
        return false;
    }

    public function forceDelete(User $user, BookCopy $bookCopy): bool
    {
        return false;
    }
}
