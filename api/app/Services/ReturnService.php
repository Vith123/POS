<?php
namespace App\Services;

use App\Models\ProductReturn;
use App\Models\Sale;
use App\Models\Inventory;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class ReturnService
{
    public function createReturn(array $data, int $userId): ProductReturn
    {
        return DB::transaction(function () use ($data, $userId) {
            $sale = Sale::with('items')->findOrFail($data['sale_id']);

            $return = ProductReturn::create([
                'sale_id' => $data['sale_id'],
                'customer_id' => $sale->customer_id,
                'product_id' => $data['product_id'],
                'quantity' => $data['quantity'],
                'reason' => $data['reason'],
                'refund_amount' => $data['refund_amount'],
                'status' => 'pending',
                'requested_by' => $userId,
            ]);

            return $return;
        });
    }

    public function updateStatus(int $id, string $status, int $userId): ProductReturn
    {
        return DB::transaction(function () use ($id, $status, $userId) {
            $return = ProductReturn::findOrFail($id);
            $return->update([
                'status' => $status,
                'processed_by' => $userId,
                'processed_at' => now(),
            ]);

            if ($status === 'approved') {
                // Return stock to inventory
                $inventory = Inventory::where('product_id', $return->product_id)->first();
                $inventory->increment('quantity', $return->quantity);

                // Record stock movement
                StockMovement::create([
                    'product_id' => $return->product_id,
                    'type' => 'in',
                    'quantity' => $return->quantity,
                    'reference' => "Return #{$return->id}",
                    'user_id' => $userId,
                ]);
            }

            return $return->fresh();
        });
    }
}
