<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'category_id' => $this->category_id,
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                ];
            }),
            'name' => $this->name,
            'description' => $this->description,
            'sku' => $this->sku,
            'barcode' => $this->barcode,
            'price' => (float) $this->selling_price,
            'cost' => (float) $this->cost_price,
            'stock_quantity' => $this->inventory ? (int) $this->inventory->quantity : 0,
            'min_stock_level' => $this->inventory ? (int) $this->inventory->min_stock_level : 0,
            'image' => $this->image,
            'image_url' => $this->image ? asset('storage/' . $this->image) : null,
            'is_active' => $this->is_active,
            'inventory' => $this->whenLoaded('inventory', function () {
                return [
                    'quantity' => $this->inventory->quantity,
                    'min_stock_level' => $this->inventory->min_stock_level,
                    'last_restocked_at' => $this->inventory->last_restocked_at?->toDateTimeString(),
                ];
            }),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
