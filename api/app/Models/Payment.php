<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'payment_method',
        'amount',
        'reference_number',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function scopeCash($query)
    {
        return $query->where('payment_method', 'cash');
    }

    public function scopeCard($query)
    {
        return $query->where('payment_method', 'card');
    }

    public function scopeEWallet($query)
    {
        return $query->where('payment_method', 'e-wallet');
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}
