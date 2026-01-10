<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    /**
     * Get all categories with optional filters
     */
    public function getAll(?bool $isActive = null, bool $withProducts = false): Collection
    {
        $query = Category::query();

        if ($isActive !== null) {
            $query->where('is_active', $isActive);
        }

        if ($withProducts) {
            $query->withCount('products');
        }

        return $query->latest()->get();
    }

    /**
     * Get single category by ID
     */
    public function getById(int $id): ?Category
    {
        return Category::withCount('products')->find($id);
    }

    /**
     * Get products in category
     */
    public function getProducts(int $id): Collection
    {
        $category = Category::find($id);

        if (!$category) {
            throw new \Exception('Category not found');
        }

        return $category->products()
            ->with('inventory')
            ->where('is_active', true)
            ->get();
    }

    /**
     * Create new category
     */
    public function create(array $data): Category
    {
        return Category::create($data);
    }

    /**
     * Update existing category
     */
    public function update(int $id, array $data): Category
    {
        $category = Category::findOrFail($id);
        $category->update($data);
        return $category->fresh();
    }

    /**
     * Delete category
     */
    public function delete(int $id): bool
    {
        $category = Category::findOrFail($id);

        if ($category->products()->count() > 0) {
            throw new \Exception('Cannot delete category with existing products');
        }

        return $category->delete();
    }
}
