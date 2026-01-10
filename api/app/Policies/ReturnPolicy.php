<?php

namespace App\Policies;

use App\Models\ProductReturn;
use App\Models\User;

class ReturnPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Both admin and cashier can view returns
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ProductReturn $return): bool
    {
        return true; // Both admin and cashier can view returns
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Both admin and cashier can create return requests
    }

    /**
     * Determine whether the user can approve returns.
     */
    public function approve(User $user): bool
    {
        return $user->role->name === 'admin'; // Only admin can approve
    }

    /**
     * Determine whether the user can reject returns.
     */
    public function reject(User $user): bool
    {
        return $user->role->name === 'admin'; // Only admin can reject
    }

    /**
     * Determine whether the user can update the return status.
     */
    public function updateStatus(User $user): bool
    {
        return $user->role->name === 'admin'; // Only admin can change status
    }
}
