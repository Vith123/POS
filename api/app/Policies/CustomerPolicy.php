<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;

class CustomerPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Both admin and cashier can view customers
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Customer $customer): bool
    {
        return true; // Both admin and cashier can view customers
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Both admin and cashier can create customers
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Customer $customer): bool
    {
        return true; // Both admin and cashier can update customers
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Customer $customer): bool
    {
        return $user->role->name === 'admin'; // Only admin can delete
    }

    /**
     * Determine whether the user can manage loyalty points.
     */
    public function manageLoyaltyPoints(User $user): bool
    {
        return true; // Both admin and cashier can manage points
    }
}
