<?php

namespace App\Policies;

use App\Models\Time;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TimePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('View Times');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Time $time): bool
    {
        return $user->hasPermissionTo('View Times');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('Create Times');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Time $time): bool
    {
        return $user->hasPermissionTo('Update Times');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Time $time): bool
    {
        return $user->hasPermissionTo('Delete Times');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Time $time): bool
    {
        return $user->hasPermissionTo('Restore Times');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Time $time): bool
    {
        return $user->hasPermissionTo('Delete Times');
    }
}
