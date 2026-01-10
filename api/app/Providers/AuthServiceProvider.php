<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\ProductReturn;
use App\Policies\CategoryPolicy;
use App\Policies\ProductPolicy;
use App\Policies\InventoryPolicy;
use App\Policies\SalePolicy;
use App\Policies\CustomerPolicy;
use App\Policies\ReturnPolicy;
use App\Policies\ReportPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<string, string>
     */
    protected $policies = [
        Category::class => CategoryPolicy::class,
        Product::class => ProductPolicy::class,
        Inventory::class => InventoryPolicy::class,
        Sale::class => SalePolicy::class,
        Customer::class => CustomerPolicy::class,
        ProductReturn::class => ReturnPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Register gates for reports (not tied to a specific model)
        Gate::define('viewDashboard', [ReportPolicy::class, 'viewDashboard']);
        Gate::define('viewSalesReport', [ReportPolicy::class, 'viewSalesReport']);
        Gate::define('viewProductPerformance', [ReportPolicy::class, 'viewProductPerformance']);
        Gate::define('viewCustomerInsights', [ReportPolicy::class, 'viewCustomerInsights']);
        Gate::define('viewInventoryReport', [ReportPolicy::class, 'viewInventoryReport']);
        Gate::define('viewFinancialSummary', [ReportPolicy::class, 'viewFinancialSummary']);
        Gate::define('viewComparativeReport', [ReportPolicy::class, 'viewComparativeReport']);
    }
}
