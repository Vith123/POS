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
