<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use AuthorizesRequests;

    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * GET ALL CATEGORIES
     *
     * Query Parameters:
     * - is_active: Filter by active status
     * - with_products: Include products count
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Category::class);

        $categories = $this->categoryService->getAll(
            $request->has('is_active') ? $request->boolean('is_active') : null,
            $request->boolean('with_products')
        );

        return CategoryResource::collection($categories);
    }

    /**
     * GET SINGLE CATEGORY
     */
    public function show(int $id): JsonResponse
    {
        $this->authorize('view', Category::class);

        $category = $this->categoryService->getById($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found',
            ], 404);
        }

        return (new CategoryResource($category))->response();
    }

    /**
     * CREATE CATEGORY
     */
    public function store(CategoryRequest $request): JsonResponse
    {
        $this->authorize('create', Category::class);

        $category = $this->categoryService->create($request->validated());

        return (new CategoryResource($category))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * UPDATE CATEGORY
     */
    public function update(CategoryRequest $request, int $id): JsonResponse
    {
        $category = Category::findOrFail($id);
        $this->authorize('update', $category);

        $updated = $this->categoryService->update($id, $request->validated());

        return (new CategoryResource($updated))->response();
    }

    /**
     * DELETE CATEGORY
     */
    public function destroy(int $id): JsonResponse
    {
        $category = Category::findOrFail($id);
        $this->authorize('delete', $category);

        try {
            $this->categoryService->delete($id);
            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * GET PRODUCTS BY CATEGORY
     */
    public function products(int $id): JsonResponse
    {
        $this->authorize('view', Category::class);

        $products = $this->categoryService->getProducts($id);

        if ($products === null) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => ProductResource::collection($products),
        ]);
    }
}
