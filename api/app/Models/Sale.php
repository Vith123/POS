<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'user_id',
        'customer_id',
        'customer_name',
        'customer_phone',
        'subtotal',
        'tax_percentage',
        'tax_amount',
        'discount_type',
        'discount_value',
        'discount_amount',
        'total_amount',
        'status',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function returns()
    {
        return $this->hasMany(ProductReturn::class);
    }

    public function getCalculatedDiscount()
    {
        if (!$this->discount_value) return 0;
        if ($this->discount_type === 'percentage') {
            return ($this->subtotal * $this->discount_value) / 100;
        }
        return $this->discount_value;
    }

    public function getTotalPaid()
    {
        return $this->payments()->sum('amount');
    }

    public function isFullyPaid()
    {
        return $this->getTotalPaid() >= $this->total_amount;
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }
}
