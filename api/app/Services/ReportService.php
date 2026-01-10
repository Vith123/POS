<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Inventory;
use App\Models\ProductReturn;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Get dashboard overview data
     */
    public function getDashboardData(): array
    {
        $today = now()->startOfDay();
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        $todaySales = $this->getSalesInRange($today, now());
        $weekSales = $this->getSalesInRange($weekStart, $weekEnd);
        $monthSales = $this->getSalesInRange($monthStart, $monthEnd);

        return [
            'today' => $this->formatPeriodStats($todaySales),
            'week' => $this->formatPeriodStats($weekSales),
            'month' => $this->formatPeriodStats($monthSales),
            'inventory' => $this->getInventoryStats(),
            'customers' => $this->getCustomerStats(),
            'pending_actions' => $this->getPendingActions(),
        ];
    }

    /**
     * Get sales report data
     */
    public function getSalesReport(string $startDate, string $endDate): array
    {
        $sales = Sale::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->with(['user', 'customer', 'items.product'])
            ->get();

        $totalCost = $this->calculateTotalCost($sales);
        $totalRevenue = $sales->sum('total_amount');
        $grossProfit = $totalRevenue - $totalCost;

        return [
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'summary' => [
                'total_sales' => $sales->count(),
                'total_revenue' => round($totalRevenue, 2),
                'total_cost' => round($totalCost, 2),
                'gross_profit' => round($grossProfit, 2),
                'profit_margin' => $totalRevenue > 0 ? round(($grossProfit / $totalRevenue) * 100, 2) . '%' : '0%',
                'total_tax' => round($sales->sum('tax_amount'), 2),
                'total_discount' => round($sales->sum('discount_amount'), 2),
                'average_sale' => $sales->count() > 0 ? round($totalRevenue / $sales->count(), 2) : 0,
            ],
            'sales_by_day' => $this->getSalesByDay($sales),
            'top_cashiers' => $this->getTopCashiers($sales),
        ];
    }

    /**
     * Get product performance report
     */
    public function getProductPerformance(string $startDate, string $endDate): array
    {
        return [
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'best_selling' => $this->getBestSellingProducts($startDate, $endDate),
            'least_selling' => $this->getLeastSellingProducts($startDate, $endDate),
            'never_sold' => $this->getNeverSoldProducts($startDate, $endDate),
            'category_performance' => $this->getCategoryPerformance($startDate, $endDate),
        ];
    }

    /**
     * Get customer insights report
     */
    public function getCustomerInsights(string $startDate, string $endDate): array
    {
        $topCustomers = $this->getTopCustomers($startDate, $endDate);
        $newCustomers = Customer::whereBetween('created_at', [$startDate, $endDate])->count();

        $totalSales = Sale::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->count();

        $salesWithCustomer = Sale::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->whereNotNull('customer_id')
            ->count();

        return [
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'top_customers' => $topCustomers,
            'acquisition' => [
                'new_customers' => $newCustomers,
            ],
            'customer_tracking' => [
                'sales_with_customer' => $salesWithCustomer,
                'sales_without_customer' => $totalSales - $salesWithCustomer,
                'tracking_rate' => $totalSales > 0 ? round(($salesWithCustomer / $totalSales) * 100, 2) . '%' : '0%',
            ],
            'customer_type_comparison' => $this->getCustomerTypeComparison($startDate, $endDate),
        ];
    }

    /**
     * Get inventory report
     */
    public function getInventoryReport(): array
    {
        $inventory = Inventory::with('product.category')
            ->get()
            ->map(function ($inv) {
                $value = $inv->quantity * $inv->product->cost_price;
                $status = $inv->quantity == 0 ? 'out_of_stock' :
                         ($inv->quantity <= $inv->min_stock_level ? 'low_stock' : 'in_stock');

                return [
                    'product_id' => $inv->product_id,
                    'name' => $inv->product->name,
                    'sku' => $inv->product->sku,
                    'category' => $inv->product->category->name,
                    'quantity' => $inv->quantity,
                    'min_stock_level' => $inv->min_stock_level,
                    'cost_price' => round($inv->product->cost_price, 2),
                    'selling_price' => round($inv->product->selling_price, 2),
                    'stock_value' => round($value, 2),
                    'status' => $status,
                ];
            });

        $totalStockValue = $inventory->sum('stock_value');
        $lowStockCount = $inventory->where('status', 'low_stock')->count();
        $outOfStockCount = $inventory->where('status', 'out_of_stock')->count();

        $movements = StockMovement::where('created_at', '>=', now()->subDays(30))
            ->select('type', DB::raw('SUM(quantity) as total_quantity'), DB::raw('COUNT(*) as movement_count'))
            ->groupBy('type')
            ->get();

        return [
            'summary' => [
                'total_products' => $inventory->count(),
                'total_stock_value' => round($totalStockValue, 2),
                'low_stock_items' => $lowStockCount,
                'out_of_stock_items' => $outOfStockCount,
                'in_stock_items' => $inventory->count() - $lowStockCount - $outOfStockCount,
            ],
            'inventory' => $inventory,
            'recent_movements' => $movements,
        ];
    }

    /**
     * Get financial summary report
     */
    public function getFinancialSummary(string $startDate, string $endDate): array
    {
        $sales = Sale::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->with('items.product')
            ->get();

        $totalRevenue = $sales->sum('total_amount');
        $totalTax = $sales->sum('tax_amount');
        $totalDiscount = $sales->sum('discount_amount');
        $subtotal = $sales->sum('subtotal');
        $totalCost = $this->calculateTotalCost($sales);

        $grossProfit = $totalRevenue - $totalCost;
        $profitMargin = $totalRevenue > 0 ? ($grossProfit / $totalRevenue) * 100 : 0;

        $paymentBreakdown = DB::table('payments')
            ->join('sales', 'payments.sale_id', '=', 'sales.id')
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->where('sales.status', 'completed')
            ->select('payments.payment_method', DB::raw('SUM(payments.amount) as total_amount'), DB::raw('COUNT(*) as count'))
            ->groupBy('payments.payment_method')
            ->get();

        $discountByType = $sales->groupBy('discount_type')
            ->map(fn($typeSales, $type) => [
                'type' => $type ?: 'none',
                'count' => $typeSales->count(),
                'total_discount' => round($typeSales->sum('discount_amount'), 2),
            ])->values();

        $returns = ProductReturn::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'approved')
            ->get();

        return [
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'revenue' => [
                'gross_sales' => round($subtotal, 2),
                'tax_collected' => round($totalTax, 2),
                'discounts_given' => round($totalDiscount, 2),
                'total_revenue' => round($totalRevenue, 2),
            ],
            'costs' => [
                'cost_of_goods_sold' => round($totalCost, 2),
            ],
            'profit' => [
                'gross_profit' => round($grossProfit, 2),
                'net_profit' => round($grossProfit, 2),
                'profit_margin' => round($profitMargin, 2) . '%',
            ],
            'payment_methods' => $paymentBreakdown,
            'discounts' => $discountByType,
            'returns' => [
                'total_returns' => $returns->count(),
                'total_refunded' => round($returns->sum('refund_amount'), 2),
            ],
        ];
    }

    /**
     * Get comparative report (compare two periods)
     */
    public function getComparativeReport(string $period1Start, string $period1End, string $period2Start, string $period2End): array
    {
        $period1Sales = $this->getSalesInRange($period1Start, $period1End);
        $period2Sales = $this->getSalesInRange($period2Start, $period2End);

        $period1Revenue = $period1Sales->sum('total_amount');
        $period2Revenue = $period2Sales->sum('total_amount');
        $period1Count = $period1Sales->count();
        $period2Count = $period2Sales->count();

        $period1Avg = $period1Count > 0 ? $period1Revenue / $period1Count : 0;
        $period2Avg = $period2Count > 0 ? $period2Revenue / $period2Count : 0;

        return [
            'period1' => [
                'start_date' => $period1Start,
                'end_date' => $period1End,
                'sales_count' => $period1Count,
                'total_revenue' => round($period1Revenue, 2),
                'average_sale' => round($period1Avg, 2),
            ],
            'period2' => [
                'start_date' => $period2Start,
                'end_date' => $period2End,
                'sales_count' => $period2Count,
                'total_revenue' => round($period2Revenue, 2),
                'average_sale' => round($period2Avg, 2),
            ],
            'changes' => [
                'revenue_change' => $this->calculatePercentageChange($period1Revenue, $period2Revenue),
                'sales_count_change' => $this->calculatePercentageChange($period1Count, $period2Count),
                'average_sale_change' => $this->calculatePercentageChange($period1Avg, $period2Avg),
            ],
        ];
    }

    // ========== Private Helper Methods ==========

    private function getSalesInRange($startDate, $endDate)
    {
        return Sale::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->get();
    }

    private function formatPeriodStats($sales): array
    {
        return [
            'sales_count' => $sales->count(),
            'revenue' => round($sales->sum('total_amount'), 2),
            'tax' => round($sales->sum('tax_amount'), 2),
            'discount' => round($sales->sum('discount_amount'), 2),
        ];
    }

    private function getInventoryStats(): array
    {
        $totalProducts = Product::where('is_active', true)->count();
        $lowStock = Product::whereHas('inventory', fn($q) => $q->whereRaw('quantity <= min_stock_level'))->count();
        $outOfStock = Product::whereHas('inventory', fn($q) => $q->where('quantity', 0))->count();

        return [
            'total_products' => $totalProducts,
            'low_stock' => $lowStock,
            'out_of_stock' => $outOfStock,
        ];
    }

    private function getCustomerStats(): array
    {
        $total = Customer::where('is_active', true)->count();
        $vip = Customer::where('type', 'vip')->where('is_active', true)->count();

        return [
            'total' => $total,
            'vip' => $vip,
            'regular' => $total - $vip,
        ];
    }

    private function getPendingActions(): array
    {
        return [
            'returns_to_process' => ProductReturn::where('status', 'pending')->count(),
        ];
    }

    private function calculateTotalCost($sales): float
    {
        $totalCost = 0;
        foreach ($sales as $sale) {
            foreach ($sale->items as $item) {
                $totalCost += $item->product->cost_price * $item->quantity;
            }
        }
        return $totalCost;
    }

    private function getSalesByDay($sales)
    {
        return $sales->groupBy(fn($sale) => $sale->created_at->format('Y-m-d'))
            ->map(fn($daySales, $date) => [
                'date' => $date,
                'sales_count' => $daySales->count(),
                'revenue' => round($daySales->sum('total_amount'), 2),
            ])->values();
    }

    private function getTopCashiers($sales)
    {
        return $sales->groupBy('user_id')
            ->map(function ($userSales) {
                $user = $userSales->first()->user;
                return [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'sales_count' => $userSales->count(),
                    'total_revenue' => round($userSales->sum('total_amount'), 2),
                ];
            })
            ->sortByDesc('total_revenue')
            ->values();
    }

    private function getBestSellingProducts(string $startDate, string $endDate)
    {
        return DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->where('sales.status', 'completed')
            ->select(
                'products.id',
                'products.name',
                'products.sku',
                'products.cost_price',
                'products.selling_price',
                DB::raw('SUM(sale_items.quantity) as total_quantity'),
                DB::raw('SUM(sale_items.subtotal) as total_revenue'),
                DB::raw('SUM(sale_items.quantity * products.cost_price) as total_cost')
            )
            ->groupBy('products.id', 'products.name', 'products.sku', 'products.cost_price', 'products.selling_price')
            ->orderBy('total_quantity', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($item) {
                $profit = $item->total_revenue - $item->total_cost;
                $margin = $item->total_revenue > 0 ? ($profit / $item->total_revenue) * 100 : 0;
                return [
                    'product_id' => $item->id,
                    'name' => $item->name,
                    'sku' => $item->sku,
                    'quantity_sold' => $item->total_quantity,
                    'revenue' => round($item->total_revenue, 2),
                    'cost' => round($item->total_cost, 2),
                    'profit' => round($profit, 2),
                    'profit_margin' => round($margin, 2) . '%',
                ];
            });
    }

    private function getLeastSellingProducts(string $startDate, string $endDate)
    {
        return DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->where('sales.status', 'completed')
            ->select(
                'products.id',
                'products.name',
                'products.sku',
                DB::raw('SUM(sale_items.quantity) as total_quantity'),
                DB::raw('SUM(sale_items.subtotal) as total_revenue')
            )
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderBy('total_quantity', 'asc')
            ->limit(10)
            ->get()
            ->map(fn($item) => [
                'product_id' => $item->id,
                'name' => $item->name,
                'sku' => $item->sku,
                'quantity_sold' => $item->total_quantity,
                'revenue' => round($item->total_revenue, 2),
            ]);
    }

    private function getNeverSoldProducts(string $startDate, string $endDate)
    {
        $soldProductIds = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->where('sales.status', 'completed')
            ->distinct()
            ->pluck('sale_items.product_id');

        return Product::whereNotIn('id', $soldProductIds)
            ->where('is_active', true)
            ->select('id', 'name', 'sku', 'selling_price')
            ->limit(10)
            ->get();
    }

    private function getCategoryPerformance(string $startDate, string $endDate)
    {
        return DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->where('sales.status', 'completed')
            ->select(
                'categories.id',
                'categories.name',
                DB::raw('SUM(sale_items.quantity) as total_quantity'),
                DB::raw('SUM(sale_items.subtotal) as total_revenue'),
                DB::raw('COUNT(DISTINCT sale_items.product_id) as products_sold')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total_revenue', 'desc')
            ->get();
    }

    private function getTopCustomers(string $startDate, string $endDate)
    {
        return Sale::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->whereNotNull('customer_id')
            ->with('customer')
            ->get()
            ->groupBy('customer_id')
            ->map(function ($customerSales) {
                $customer = $customerSales->first()->customer;
                return [
                    'customer_id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'type' => $customer->type,
                    'purchase_count' => $customerSales->count(),
                    'total_spent' => round($customerSales->sum('total_amount'), 2),
                    'average_order' => round($customerSales->avg('total_amount'), 2),
                    'loyalty_points' => $customer->loyalty_points,
                ];
            })
            ->sortByDesc('total_spent')
            ->take(20)
            ->values();
    }

    private function getCustomerTypeComparison(string $startDate, string $endDate): array
    {
        $vipSales = Sale::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->whereHas('customer', fn($q) => $q->where('type', 'vip'))
            ->get();

        $regularSales = Sale::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->whereHas('customer', fn($q) => $q->where('type', 'regular'))
            ->get();

        return [
            'vip' => [
                'sales_count' => $vipSales->count(),
                'total_revenue' => round($vipSales->sum('total_amount'), 2),
                'average_order' => $vipSales->count() > 0 ? round($vipSales->avg('total_amount'), 2) : 0,
            ],
            'regular' => [
                'sales_count' => $regularSales->count(),
                'total_revenue' => round($regularSales->sum('total_amount'), 2),
                'average_order' => $regularSales->count() > 0 ? round($regularSales->avg('total_amount'), 2) : 0,
            ],
        ];
    }

    private function calculatePercentageChange($oldValue, $newValue): string
    {
        if ($oldValue == 0) {
            return $newValue > 0 ? '100%' : '0%';
        }
        $change = (($newValue - $oldValue) / $oldValue) * 100;
        return round($change, 2) . '%';
    }
}
