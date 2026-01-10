<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductReturn extends Model
{
    protected $table = 'returns';

    protected $fillable = [
        'return_number',
        'sale_id',
        'product_id',
        'user_id',
        'quantity',
        'refund_amount',
        'reason',
        'notes',
        'status'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'refund_amount' => 'decimal:2'
    ];

    /**
     * Get the sale for this return
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Get the product for this return
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user who processed this return
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for pending returns
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved returns
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Check if return is pending
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if return is approved
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }
}
