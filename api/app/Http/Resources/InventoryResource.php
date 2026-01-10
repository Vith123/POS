<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryResource extends JsonResource
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
            'product_id' => $this->product_id,
            'product' => $this->whenLoaded('product', function () {
                return [
                    'id' => $this->product->id,
                    'name' => $this->product->name,
                    'sku' => $this->product->sku,
                    'image' => $this->product->image,
                ];
            }),
            'quantity' => $this->quantity,
            'min_stock' => $this->min_stock,
            'max_stock' => $this->max_stock,
            'last_restocked_at' => $this->last_restocked_at?->toDateTimeString(),
            'stock_status' => $this->when(isset($this->stock_status), $this->stock_status),
            'stock_value' => $this->when(isset($this->stock_value), (float) $this->stock_value),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
