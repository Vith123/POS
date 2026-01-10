<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'permissions',
    ];

    protected $casts = [
        'permissions' => 'array', // Automatically convert JSON to array
    ];

    /**
     * RELATIONSHIP: Role has many Users
     * Many users can have the same role
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * HELPER: Check if role has a specific permission
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermission($permission)
    {
        if (!$this->permissions) {
            return false;
        }

        return in_array($permission, $this->permissions);
    }
}
