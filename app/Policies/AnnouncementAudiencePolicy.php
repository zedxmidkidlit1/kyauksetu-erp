<?php

namespace App\Policies;

use App\Models\AnnouncementAudience;
use App\Models\User;

class AnnouncementAudiencePolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('announcement_audiences.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AnnouncementAudience $announcementAudience): bool
    {
        return $user->can('announcement_audiences.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('announcement_audiences.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AnnouncementAudience $announcementAudience): bool
    {
        return $user->can('announcement_audiences.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AnnouncementAudience $announcementAudience): bool
    {
        return $user->can('announcement_audiences.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AnnouncementAudience $announcementAudience): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AnnouncementAudience $announcementAudience): bool
    {
        return false;
    }
}
