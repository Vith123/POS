<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReportPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any reports.
     */
    public function viewAny(User $user): bool
    {
        // Both admin and cashier can view reports
        return in_array($user->role->name, ['admin', 'cashier']);
    }

    /**
     * Determine if the user can view dashboard.
     */
    public function viewDashboard(User $user): bool
    {
        return in_array($user->role->name, ['admin', 'cashier']);
    }

    /**
     * Determine if the user can view sales reports.
     */
    public function viewSalesReport(User $user): bool
    {
        return in_array($user->role->name, ['admin', 'cashier']);
    }

    /**
     * Determine if the user can view product performance reports.
     */
    public function viewProductPerformance(User $user): bool
    {
        return in_array($user->role->name, ['admin', 'cashier']);
    }

    /**
     * Determine if the user can view customer insights.
     */
    public function viewCustomerInsights(User $user): bool
    {
        return in_array($user->role->name, ['admin', 'cashier']);
    }

    /**
     * Determine if the user can view inventory reports.
     */
    public function viewInventoryReport(User $user): bool
    {
        return in_array($user->role->name, ['admin', 'cashier']);
    }

    /**
     * Determine if the user can view financial summaries.
     * Only admin can view financial data.
     */
    public function viewFinancialSummary(User $user): bool
    {
        return $user->role->name === 'admin';
    }

    /**
     * Determine if the user can view comparative reports.
     * Only admin can view comparative data.
     */
    public function viewComparativeReport(User $user): bool
    {
        return $user->role->name === 'admin';
    }
}
