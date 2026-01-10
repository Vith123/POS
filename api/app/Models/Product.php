<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'barcode',
        'sku',
        'description',
        'cost_price',
        'selling_price',
        'image',
        'is_active',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function getProfitMargin()
    {
        if ($this->selling_price <= 0) return 0;
        $profit = $this->selling_price - $this->cost_price;
        return ($profit / $this->selling_price) * 100;
    }

    public function getStockQuantity()
    {
        return $this->inventory ? $this->inventory->quantity : 0;
    }

    public function isLowStock()
    {
        if (!$this->inventory) return true;
        return $this->inventory->quantity <= $this->inventory->min_stock_level;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('barcode', 'like', "%{$term}%")
              ->orWhere('sku', 'like', "%{$term}%");
        });
    }
}
