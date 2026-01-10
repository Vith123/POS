<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $this->resource['user']->id,
                    'name' => $this->resource['user']->name,
                    'email' => $this->resource['user']->email,
                    'role' => [
                        'id' => $this->resource['user']->role->id,
                        'name' => $this->resource['user']->role->name,
                    ],
                    'is_active' => $this->resource['user']->is_active,
                    'created_at' => $this->resource['user']->created_at->toDateTimeString(),
                ],
                'token' => $this->resource['token'] ?? null,
            ],
            'message' => $this->resource['message'] ?? 'Operation successful',
            'timestamp' => now()->toDateTimeString(),
        ];
    }
}
