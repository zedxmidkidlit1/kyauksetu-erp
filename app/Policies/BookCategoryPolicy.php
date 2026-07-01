<?php

namespace App\Policies;

use App\Models\BookCategory;
use App\Models\User;

class BookCategoryPolicy
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
        return $user->can('book_categories.view');
    }

    public function view(User $user, BookCategory $bookCategory): bool
    {
        return $user->can('book_categories.view');
    }

    public function create(User $user): bool
    {
        return $user->can('book_categories.create');
    }

    public function update(User $user, BookCategory $bookCategory): bool
    {
        return $user->can('book_categories.update');
    }

    public function delete(User $user, BookCategory $bookCategory): bool
    {
        return $user->can('book_categories.delete');
    }

    public function restore(User $user, BookCategory $bookCategory): bool
    {
        return false;
    }

    public function forceDelete(User $user, BookCategory $bookCategory): bool
    {
        return false;
    }
}
