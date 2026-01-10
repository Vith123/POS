<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'type',
        'credit_limit',
        'credit_balance',
        'loyalty_points',
        'is_active'
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'credit_balance' => 'decimal:2',
        'loyalty_points' => 'integer',
        'is_active' => 'boolean'
    ];

    /**
     * Get sales for this customer
     */
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Check if customer is VIP
     */
    public function isVip()
    {
        return $this->type === 'vip';
    }

    /**
     * Get available credit
     */
    public function getAvailableCreditAttribute()
    {
        return $this->credit_limit - $this->credit_balance;
    }

    /**
     * Check if customer can use credit
     */
    public function canUseCredit($amount)
    {
        return $this->available_credit >= $amount;
    }

    /**
     * Add loyalty points
     */
    public function addLoyaltyPoints($points)
    {
        $this->increment('loyalty_points', $points);
    }

    /**
     * Deduct loyalty points
     */
    public function deductLoyaltyPoints($points)
    {
        if ($this->loyalty_points >= $points) {
            $this->decrement('loyalty_points', $points);
            return true;
        }
        return false;
    }

    /**
     * Scope for active customers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for VIP customers
     */
    public function scopeVip($query)
    {
        return $query->where('type', 'vip');
    }

    /**
     * Scope for search
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%");
        });
    }
}
