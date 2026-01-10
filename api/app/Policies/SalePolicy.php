<?php

namespace App\Policies;

use App\Models\Sale;
use App\Models\User;

class SalePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Both admin and cashier can view sales
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Sale $sale): bool
    {
        return true; // Both admin and cashier can view sales
    }

    /**
     * Determine whether the user can create models (process checkout).
     */
    public function create(User $user): bool
    {
        return true; // Both admin and cashier can create sales
    }

    /**
     * Determine whether the user can view sale statistics.
     */
    public function viewStatistics(User $user): bool
    {
        return true; // Both admin and cashier can view statistics
    }
}
