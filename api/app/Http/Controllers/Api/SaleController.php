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

    public function todaySummary(): JsonResponse
    {
        $this->authorize('viewAny', Sale::class);

        $today = now()->toDateString();

        $todaySales = Sale::whereDate('created_at', $today)->get();

        $summary = [
            'count' => $todaySales->count(),
            'revenue' => $todaySales->sum('total'),
            'avg_sale' => $todaySales->avg('total') ?? 0,
        ];

        return response()->json(['success' => true, 'data' => $summary]);
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
