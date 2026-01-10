<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use AuthorizesRequests;

    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Product::class);

        $filters = [
            'search' => $request->search,
            'category_id' => $request->category_id,
            'is_active' => $request->is_active,
            'with_inventory' => $request->boolean('with_inventory'),
        ];

        $products = $this->productService->getAll($filters, $request->per_page ?? 15);

        return ProductResource::collection($products);
    }

    public function show(int $id): JsonResponse
    {
        $this->authorize('view', Product::class);

        $product = $this->productService->getById($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        }

        return (new ProductResource($product))->response();
    }

    public function store(ProductRequest $request): JsonResponse
    {
        $this->authorize('create', Product::class);

        try {
            $product = $this->productService->create($request->validated());

            return (new ProductResource($product))
                ->response()
                ->setStatusCode(201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create product',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(ProductRequest $request, int $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        $this->authorize('update', $product);

        try {
            $updated = $this->productService->update($id, $request->validated());

            return (new ProductResource($updated))->response();
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update product',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        $this->authorize('delete', $product);

        try {
            $this->productService->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function lowStock(): JsonResponse
    {
        $this->authorize('viewAny', Product::class);

        $products = $this->productService->getLowStock();

        return response()->json([
            'success' => true,
            'count' => $products->count(),
            'data' => ProductResource::collection($products),
        ]);
    }

    public function outOfStock(): JsonResponse
    {
        $this->authorize('viewAny', Product::class);

        $products = $this->productService->getOutOfStock();

        return response()->json([
            'success' => true,
            'count' => $products->count(),
            'data' => ProductResource::collection($products),
        ]);
    }
}
