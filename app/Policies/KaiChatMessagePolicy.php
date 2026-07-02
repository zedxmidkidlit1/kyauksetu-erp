<?php

namespace App\Policies;

use App\Models\KaiChatMessage;
use App\Models\User;

class KaiChatMessagePolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('super_admin') && in_array($ability, ['viewAny', 'view'], true)) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('kai_chat_messages.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, KaiChatMessage $kaiChatMessage): bool
    {
        return $user->can('kai_chat_messages.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, KaiChatMessage $kaiChatMessage): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, KaiChatMessage $kaiChatMessage): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, KaiChatMessage $kaiChatMessage): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, KaiChatMessage $kaiChatMessage): bool
    {
        return false;
    }
}
