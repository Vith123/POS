<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * RELATIONSHIP: User belongs to a Role
     * A user has one role (admin, cashier, etc.)
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * RELATIONSHIP: User has many Sales
     * Track which cashier made which sale
     */
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * RELATIONSHIP: User has many Stock Movements
     * Audit trail - who recorded stock changes
     */
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * HELPER: Check if user is admin
     */
    public function isAdmin()
    {
        return $this->role && $this->role->name === 'admin';
    }

    /**
     * HELPER: Check if user is cashier
     */
    public function isCashier()
    {
        return $this->role && $this->role->name === 'cashier';
    }
}
