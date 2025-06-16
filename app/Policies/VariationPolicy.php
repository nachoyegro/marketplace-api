<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Variation;
use Illuminate\Auth\Access\Response;
use App\Enums\UserRole;

class VariationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return  $user->role === UserRole::ADMIN ||
                $user->role === UserRole::ADMIN_COMPANY ||
                $user->role === UserRole::USER_COMPANY;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Variation $variation): bool
    {
        return  $user->role === UserRole::ADMIN ||
                $user->role === UserRole::ADMIN_COMPANY ||
                $user->role === UserRole::USER_COMPANY;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return  $user->role === UserRole::ADMIN;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Variation $variation): bool
    {
        return  $user->role === UserRole::ADMIN;
    }

    /**
     * Determine whether the user redeem a variation.
     */
    public function redeem(User $user, Variation $variation): bool
    {
        return  $user->role === UserRole::USER_COMPANY;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Variation $variation): bool
    {
        return  $user->role === UserRole::ADMIN;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Variation $variation): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Variation $variation): bool
    {
        return false;
    }
}
