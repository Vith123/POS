<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'quantity',
        'min_stock_level',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'min_stock_level' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function isLowStock()
    {
        return $this->quantity <= $this->min_stock_level;
    }

    public function isOutOfStock()
    {
        return $this->quantity <= 0;
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('quantity', '<=', 'min_stock_level');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('quantity', '<=', 0);
    }
}
