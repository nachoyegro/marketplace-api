<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Company;
use App\Models\User;
use App\Enums\UserRole;

class CompanyPolicy
{
    /**
     * Determine whether the user can view any models.
     * This is only allowed for:
     * - Admins
     */
    public function viewAny(User $user): bool
    {
        return  $user->role === UserRole::ADMIN;
    }

    /**
     * Determine whether the user can view the model.
     * This is only allowed for:
     * - Admins
     * - Admins of the company
     */
    public function view(User $user, Company $company): bool
    {
        return match ($user->role) {
            UserRole::ADMIN => true,
            UserRole::ADMIN_COMPANY => $company->id === $user->getCompanyId(),
            UserRole::USER_COMPANY => $company->id === $user->getCompanyId(),
            default => false,
        };
    }

    /**
     * Determine whether the user can create models.
     * This is only allowed for:
     * - Admins
     */
    public function create(User $user): bool
    {
        return  $user->role === UserRole::ADMIN;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Company $company): bool
    {
        return  $user->role === UserRole::ADMIN;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Company $company): bool
    {
        return  $user->role === UserRole::ADMIN;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Company $company): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Company $company): bool
    {
        return false;
    }
}
