#!/bin/bash

# Backup originals
cp app/Http/Controllers/Api/SaleController.php app/Http/Controllers/Api/SaleController.php.bak
cp app/Http/Controllers/Api/CustomerController.php app/Http/Controllers/Api/CustomerController.php.bak
cp app/Http/Controllers/Api/ReturnController.php app/Http/Controllers/Api/ReturnController.php.bak

# Create SaleController
cat > app/Http/Controllers/Api/SaleController.php << 'PHPEOF'
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaleRequest;
use App\Http\Resources\SaleResource;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    use AuthorizesRequests;

    protected SaleService $saleService;

    public function __construct(SaleService $saleService)
    {
        $this->saleService = $saleService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Sale::class);

        $query = Sale::with(['user', 'customer', 'saleItems.product', 'payments']);

        if ($request->has('status')) $query->where('status', $request->status);
        if ($request->has('start_date')) $query->whereDate('created_at', '>=', $request->start_date);
        if ($request->has('end_date')) $query->whereDate('created_at', '<=', $request->end_date);
        if ($request->has('user_id')) $query->where('user_id', $request->user_id);
        if ($request->has('customer_id')) $query->where('customer_id', $request->customer_id);

        $sales = $query->latest()->paginate($request->per_page ?? 15);

        return SaleResource::collection($sales);
    }

    public function show(int $id): JsonResponse
    {
        $this->authorize('view', Sale::class);

        $sale = Sale::with(['user', 'customer', 'saleItems.product', 'payments'])->find($id);

        if (!$sale) {
            return response()->json(['success' => false, 'message' => 'Sale not found'], 404);
        }

        return (new SaleResource($sale))->response();
    }

    public function store(SaleRequest $request): JsonResponse
    {
        $this->authorize('create', Sale::class);

        try {
            $sale = $this->saleService->createSale($request->validated(), $request->user()->id);

            return (new SaleResource($sale->load(['user', 'customer', 'saleItems.product', 'payments'])))
                ->response()
                ->setStatusCode(201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Checkout failed',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    public function statistics(Request $request): JsonResponse
    {
        $this->authorize('viewStatistics', Sale::class);

        $startDate = $request->start_date ?? now()->subDays(30)->toDateString();
        $endDate = $request->end_date ?? now()->toDateString();

        $stats = [
            'total_sales' => Sale::whereBetween('created_at', [$startDate, $endDate])->count(),
            'total_revenue' => Sale::whereBetween('created_at', [$startDate, $endDate])->sum('total'),
            'avg_sale_value' => Sale::whereBetween('created_at', [$startDate, $endDate])->avg('total'),
        ];

        return response()->json(['success' => true, 'data' => $stats]);
    }
}
PHPEOF

# Create CustomerController
cat > app/Http/Controllers/Api/CustomerController.php << 'PHPEOF'
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Services\CustomerService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    use AuthorizesRequests;

    protected CustomerService $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Customer::class);

        $filters = [
            'search' => $request->search,
            'is_vip' => $request->is_vip,
        ];

        $customers = $this->customerService->getAll($filters, $request->per_page ?? 15);

        return CustomerResource::collection($customers);
    }

    public function show(int $id): JsonResponse
    {
        $this->authorize('view', Customer::class);

        $customer = $this->customerService->getById($id);

        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'Customer not found'], 404);
        }

        return (new CustomerResource($customer))->response();
    }

    public function store(CustomerRequest $request): JsonResponse
    {
        $this->authorize('create', Customer::class);

        $customer = $this->customerService->create($request->validated());

        return (new CustomerResource($customer))
            ->response()
            ->setStatusCode(201);
    }

    public function update(CustomerRequest $request, int $id): JsonResponse
    {
        $customer = Customer::findOrFail($id);
        $this->authorize('update', $customer);

        $updated = $this->customerService->update($id, $request->validated());

        return (new CustomerResource($updated))->response();
    }

    public function destroy(int $id): JsonResponse
    {
        $customer = Customer::findOrFail($id);
        $this->authorize('delete', $customer);

        try {
            $this->customerService->delete($id);
            return response()->json(['success' => true, 'message' => 'Customer deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function addPoints(Request $request, int $id): JsonResponse
    {
        $this->authorize('manageLoyaltyPoints', Customer::class);

        $request->validate(['points' => 'required|integer|min:1']);

        $customer = $this->customerService->manageLoyaltyPoints($id, 'add', $request->points);

        return (new CustomerResource($customer))->response();
    }

    public function subtractPoints(Request $request, int $id): JsonResponse
    {
        $this->authorize('manageLoyaltyPoints', Customer::class);

        $request->validate(['points' => 'required|integer|min:1']);

        $customer = $this->customerService->manageLoyaltyPoints($id, 'subtract', $request->points);

        return (new CustomerResource($customer))->response();
    }
}
PHPEOF

# Create ReturnController
cat > app/Http/Controllers/Api/ReturnController.php << 'PHPEOF'
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReturnRequest;
use App\Http\Resources\ReturnResource;
use App\Models\ProductReturn;
use App\Services\ReturnService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReturnController extends Controller
{
    use AuthorizesRequests;

    protected ReturnService $returnService;

    public function __construct(ReturnService $returnService)
    {
        $this->returnService = $returnService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', ProductReturn::class);

        $query = ProductReturn::with(['sale', 'product', 'processor']);

        if ($request->has('status')) $query->where('status', $request->status);
        if ($request->has('start_date')) $query->whereDate('created_at', '>=', $request->start_date);
        if ($request->has('end_date')) $query->whereDate('created_at', '<=', $request->end_date);
        if ($request->has('product_id')) $query->where('product_id', $request->product_id);

        $returns = $query->latest()->paginate($request->per_page ?? 15);

        return ReturnResource::collection($returns);
    }

    public function show(int $id): JsonResponse
    {
        $this->authorize('view', ProductReturn::class);

        $return = ProductReturn::with(['sale', 'product', 'processor'])->find($id);

        if (!$return) {
            return response()->json(['success' => false, 'message' => 'Return not found'], 404);
        }

        return (new ReturnResource($return))->response();
    }

    public function store(ReturnRequest $request): JsonResponse
    {
        $this->authorize('create', ProductReturn::class);

        try {
            $return = $this->returnService->createReturn($request->validated(), $request->user()->id);

            return (new ReturnResource($return->load(['sale', 'product'])))
                ->response()
                ->setStatusCode(201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create return',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    public function approve(int $id, Request $request): JsonResponse
    {
        $this->authorize('approve', ProductReturn::class);

        try {
            $return = $this->returnService->updateStatus($id, 'approved', $request->user()->id);

            return (new ReturnResource($return))->response();
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function reject(int $id, Request $request): JsonResponse
    {
        $this->authorize('reject', ProductReturn::class);

        try {
            $return = $this->returnService->updateStatus($id, 'rejected', $request->user()->id);

            return (new ReturnResource($return))->response();
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
PHPEOF

echo "✅ All controllers refactored successfully!"
