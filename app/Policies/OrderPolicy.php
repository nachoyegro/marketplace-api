<?php

namespace App\Policies;


use Illuminate\Auth\Access\Response;
use App\Models\Order;
use App\Models\User;
use App\Enums\UserRole;

class OrderPolicy
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
     * This is only allowed for:
     * - Admins
     * - Admins of the company the order belongs to
     * - The user who is the the owner of the order
     */
    public function view(User $user, Order $order): bool
    {
        return match ($user->role) {
            UserRole::ADMIN => true,
            UserRole::ADMIN_COMPANY => $order->company_id === $user->getCompanyId(),
            UserRole::USER_COMPANY => $order->employee_id === $user->getEmployeeId(),
            default => false,
        };
    }

    /**
     * Determine whether the user can create models.
     * This is only allowed for:
     * - Admins
     * - Employees of a company
     */
    public function create(User $user): bool
    {
        return  $user->role === UserRole::ADMIN || 
                $user->role === UserRole::USER_COMPANY;
    }

    /**
     * Determine whether the user can update the model.
     * This is only allowed for:
     * - Admins
     * - Employees of a company
     */
    public function update(User $user, Order $order): bool
    {
        return  $user->role === UserRole::ADMIN || 
                $user->role === UserRole::USER_COMPANY;
    }

    /**
     * Determine whether the user can delete the model.
     * This is only allowed for:
     * - Admins
     * - Employees of a company
     */
    public function delete(User $user, Order $order): bool
    {
        return  $user->role === UserRole::ADMIN || 
                $user->role === UserRole::USER_COMPANY;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Order $order): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Order $order): bool
    {
        return false;
    }
}
