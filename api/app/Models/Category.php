<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * RELATIONSHIP: Category has many Products
     * One category contains multiple products
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * HELPER: Get only active products in this category
     */
    public function activeProducts()
    {
        return $this->hasMany(Product::class)->where('is_active', true);
    }

    /**
     * SCOPE: Get only active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
