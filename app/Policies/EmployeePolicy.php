<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Employee;
use App\Models\User;
use App\Enums\UserRole;

class EmployeePolicy
{
    /**
     * Determine whether the user can view any models.
     * This is only allowed for:
     * - Admins
     * - Admins of the company
     */
    public function viewAny(User $user): bool
    {
        return  $user->role === UserRole::ADMIN || 
                $user->role === UserRole::ADMIN_COMPANY;
    }

    /**
     * Determine whether the user can view the model.
     * This is only allowed for:
     * - Admins
     * - Admins of the company the employee belongs to
     * - The user who is the employee
     */
    public function view(User $user, Employee $employee): bool
    {
        return match ($user->role) {
            UserRole::ADMIN => true,
            UserRole::ADMIN_COMPANY => $employee->company_id === $user->getCompanyId(),
            UserRole::USER_COMPANY => $employee->user_id === $user->id,
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
     * This is only allowed for:
     * - Admins
     */
    public function update(User $user, Employee $employee): bool
    {
        return $user->role === UserRole::ADMIN;
    }

    /**
     * Determine whether the user can delete the model.
     * This is only allowed for:
     * - Admins
     */
    public function delete(User $user, Employee $employee): bool
    {
        return $user->role === UserRole::ADMIN;
    }

    /**
     * Determine whether the user can restore the model.
     * This is only allowed for:
     * - Admins
     */
    public function restore(User $user, Employee $employee): bool
    {
        return $user->role === UserRole::ADMIN;
    }

    /**
     * Determine whether the user can permanently delete the model.
     * This is only allowed for:
     * - Admins
     */
    public function forceDelete(User $user, Employee $employee): bool
    {
        return $user->role === UserRole::ADMIN;
    }
}
