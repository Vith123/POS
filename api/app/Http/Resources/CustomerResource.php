<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'city' => $this->city,
            'postal_code' => $this->postal_code,
            'is_vip' => $this->is_vip,
            'loyalty_points' => $this->loyalty_points,
            'total_purchases' => $this->when(isset($this->total_purchases), (float) $this->total_purchases),
            'total_orders' => $this->when(isset($this->total_orders), $this->total_orders),
            'sales' => $this->whenLoaded('sales', function () {
                return SaleResource::collection($this->sales);
            }),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
