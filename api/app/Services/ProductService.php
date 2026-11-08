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

        if (isset($filters['is_active']) && $filters['is_active'] !== '' && $filters['is_active'] !== null) {
            $isActive = filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($isActive !== null) {
                $query->where('is_active', $isActive);
            }
        }

        if (($filters['stock'] ?? null) === 'low') {
            $query->whereHas('inventory', function ($inventory) {
                $inventory->whereColumn('quantity', '<=', 'min_stock_level')
                    ->where('quantity', '>', 0);
            });
        }

        if (($filters['stock'] ?? null) === 'out') {
            $query->whereHas('inventory', function ($inventory) {
                $inventory->where('quantity', '<=', 0);
            });
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
                'cost_price' => $data['cost'] ?? 0,
                'selling_price' => $data['price'],
                'image' => $data['image'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ]);

            Inventory::create([
                'product_id' => $product->id,
                'quantity' => $data['stock_quantity'] ?? 0,
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

        $attributes = [];

        foreach ([
            'category_id' => 'category_id',
            'name' => 'name',
            'description' => 'description',
            'barcode' => 'barcode',
            'sku' => 'sku',
            'cost' => 'cost_price',
            'price' => 'selling_price',
            'image' => 'image',
            'is_active' => 'is_active',
        ] as $input => $column) {
            if (array_key_exists($input, $data)) {
                $attributes[$column] = $data[$input];
            }
        }

        if ($attributes !== []) {
            $product->update($attributes);
        }

        $inventoryAttributes = [];
        if (array_key_exists('stock_quantity', $data)) {
            $inventoryAttributes['quantity'] = $data['stock_quantity'];
        }
        if (array_key_exists('min_stock_level', $data) && $data['min_stock_level'] !== null) {
            $inventoryAttributes['min_stock_level'] = $data['min_stock_level'];
        }

        if ($inventoryAttributes !== []) {
            $product->inventory()->updateOrCreate(
                ['product_id' => $product->id],
                $inventoryAttributes
            );
        }

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
