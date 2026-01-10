<?php

namespace App\Policies;

use App\Models\Inventory;
use App\Models\User;

class InventoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Both admin and cashier can view inventory
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Inventory $inventory): bool
    {
        return true; // Both admin and cashier can view inventory
    }

    /**
     * Determine whether the user can perform stock in operations.
     */
    public function stockIn(User $user): bool
    {
        return $user->role->name === 'admin';
    }

    /**
     * Determine whether the user can perform stock out operations.
     */
    public function stockOut(User $user): bool
    {
        return $user->role->name === 'admin';
    }

    /**
     * Determine whether the user can view stock movements.
     */
    public function viewMovements(User $user): bool
    {
        return true; // Both admin and cashier can view movements
    }
}
