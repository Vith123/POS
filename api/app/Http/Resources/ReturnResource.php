<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReturnResource extends JsonResource
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
            'sale_id' => $this->sale_id,
            'sale' => $this->whenLoaded('sale', function () {
                return [
                    'id' => $this->sale->id,
                    'invoice_number' => $this->sale->invoice_number,
                ];
            }),
            'product_id' => $this->product_id,
            'product' => $this->whenLoaded('product', function () {
                return [
                    'id' => $this->product->id,
                    'name' => $this->product->name,
                    'sku' => $this->product->sku,
                ];
            }),
            'quantity' => $this->quantity,
            'reason' => $this->reason,
            'refund_amount' => (float) $this->refund_amount,
            'status' => $this->status,
            'processed_by' => $this->processed_by,
            'processor' => $this->whenLoaded('processor', function () {
                return [
                    'id' => $this->processor->id,
                    'name' => $this->processor->name,
                ];
            }),
            'processed_at' => $this->processed_at?->toDateTimeString(),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
