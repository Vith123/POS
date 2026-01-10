<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductService
{
    /**
     * Get all products with filters and pagination
     */
    public function getAll(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = Product::query()->with(['category', 'inventory']);

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['is_active'])) {
            // Convert string 'true'/'false' to boolean
            $isActive = filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($isActive !== null) {
                $query->where('is_active', $isActive);
            }
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Get single product by ID
     */
    public function getById(int $id): ?Product
    {
        return Product::with(['category', 'inventory'])->find($id);
    }

    /**
     * Get low stock products
     */
    public function getLowStock()
    {
        return Product::with(['category', 'inventory'])
            ->whereHas('inventory', function ($query) {
                $query->whereRaw('quantity <= min_stock_level')
                      ->where('quantity', '>', 0);
            })
            ->get();
    }

    /**
     * Get out of stock products
     */
    public function getOutOfStock()
    {
        return Product::with(['category', 'inventory'])
            ->whereHas('inventory', function ($query) {
                $query->where('quantity', 0);
            })
            ->get();
    }

    /**
     * Create new product with inventory
     */
    public function create(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            $product = Product::create([
                'category_id' => $data['category_id'],
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'barcode' => $data['barcode'] ?? null,
                'sku' => $data['sku'],
                'cost_price' => $data['cost_price'],
                'selling_price' => $data['selling_price'],
                'image' => $data['image'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ]);

            // Create inventory record
            Inventory::create([
                'product_id' => $product->id,
                'quantity' => $data['initial_quantity'] ?? 0,
                'min_stock_level' => $data['min_stock_level'] ?? 10,
            ]);

            return $product->load(['category', 'inventory']);
        });
    }

    /**
     * Update existing product
     */
    public function update(int $id, array $data): Product
    {
        $product = Product::findOrFail($id);

        if (!empty($data['image'])) {
            // Delete old image if exists
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
        }

        $product->update(array_filter([
            'category_id' => $data['category_id'] ?? null,
            'name' => $data['name'] ?? null,
            'description' => $data['description'] ?? null,
            'barcode' => $data['barcode'] ?? null,
            'sku' => $data['sku'] ?? null,
            'cost_price' => $data['cost_price'] ?? null,
            'selling_price' => $data['selling_price'] ?? null,
            'image' => $data['image'] ?? null,
            'is_active' => isset($data['is_active']) ? $data['is_active'] : null,
        ], fn($value) => $value !== null));

        return $product->fresh(['category', 'inventory']);
    }

    /**
     * Delete product
     */
    public function delete(int $id): bool
    {
        $product = Product::findOrFail($id);

        // Delete image if exists
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        // Delete inventory record
        $product->inventory()->delete();

        return $product->delete();
    }

    /**
     * Handle image upload
     */
    public function uploadImage($file): string
    {
        return $file->store('products', 'public');
    }
}
