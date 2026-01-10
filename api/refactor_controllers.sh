#!/bin/bash

# Refactor InventoryController
cat > app/Http/Controllers/Api/InventoryController.php << 'PHPEOF'
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InventoryStockRequest;
use App\Http\Resources\InventoryResource;
use App\Models\Inventory;
use App\Services\InventoryService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    use AuthorizesRequests;

    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Inventory::class);
        $result = $this->inventoryService->getAllInventory();
        return response()->json([
            'success' => true,
            'summary' => $result['summary'],
            'inventory' => InventoryResource::collection($result['inventory']),
        ]);
    }

    public function show(int $productId): JsonResponse
    {
        $this->authorize('view', Inventory::class);
        $product = \App\Models\Product::with(['inventory', 'stockMovements.user'])->find($productId);
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }
        return response()->json([
            'success' => true,
            'data' => [
                'product' => ['id' => $product->id, 'name' => $product->name, 'sku' => $product->sku],
                'inventory' => new InventoryResource($product->inventory),
                'recent_movements' => $product->stockMovements()->latest()->limit(10)->get(),
            ],
        ]);
    }

    public function stockIn(InventoryStockRequest $request): JsonResponse
    {
        $this->authorize('stockIn', Inventory::class);
        try {
            $inventory = $this->inventoryService->stockIn(
                $request->validated()['product_id'],
                $request->validated()['quantity'],
                $request->validated()['reference'],
                $request->validated()['notes'] ?? null,
                $request->user()->id
            );
            return response()->json([
                'success' => true,
                'message' => "Added {$request->quantity} units successfully",
                'data' => new InventoryResource($inventory),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function stockOut(InventoryStockRequest $request): JsonResponse
    {
        $this->authorize('stockOut', Inventory::class);
        try {
            $inventory = $this->inventoryService->stockOut(
                $request->validated()['product_id'],
                $request->validated()['quantity'],
                $request->validated()['reference'],
                $request->validated()['notes'] ?? null,
                $request->user()->id
            );
            return response()->json([
                'success' => true,
                'message' => "Removed {$request->quantity} units successfully",
                'data' => new InventoryResource($inventory),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function movements(Request $request): JsonResponse
    {
        $this->authorize('viewMovements', Inventory::class);
        $query = \App\Models\StockMovement::with(['product', 'user']);
        if ($request->product_id) $query->where('product_id', $request->product_id);
        if ($request->type) $query->where('type', $request->type);
        $movements = $query->latest()->paginate($request->per_page ?? 20);
        return response()->json(['success' => true, 'data' => $movements]);
    }
}
PHPEOF

echo "✅ InventoryController refactored"
