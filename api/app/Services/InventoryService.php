<?php
namespace App\Services;

use App\Models\Product;
use App\Models\Inventory;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    public function getAllInventory()
    {
        $inventory = Inventory::with('product.category')->get();
        
        return [
            'summary' => $this->calculateSummary($inventory),
            'inventory' => $inventory,
        ];
    }

    public function stockIn(int $productId, int $quantity, string $reference, ?string $notes, int $userId): Inventory
    {
        return DB::transaction(function () use ($productId, $quantity, $reference, $notes, $userId) {
            $inventory = Inventory::where('product_id', $productId)->firstOrFail();
            $inventory->increment('quantity', $quantity);

            StockMovement::create([
                'product_id' => $productId,
                'type' => 'in',
                'quantity' => $quantity,
                'reference' => $reference,
                'notes' => $notes,
                'user_id' => $userId,
            ]);

            return $inventory->fresh();
        });
    }

    public function stockOut(int $productId, int $quantity, string $reference, ?string $notes, int $userId): Inventory
    {
        return DB::transaction(function () use ($productId, $quantity, $reference, $notes, $userId) {
            $inventory = Inventory::where('product_id', $productId)->firstOrFail();

            if ($inventory->quantity < $quantity) {
                throw new \Exception('Insufficient stock');
            }

            $inventory->decrement('quantity', $quantity);

            StockMovement::create([
                'product_id' => $productId,
                'type' => 'out',
                'quantity' => $quantity,
                'reference' => $reference,
                'notes' => $notes,
                'user_id' => $userId,
            ]);

            return $inventory->fresh();
        });
    }

    private function calculateSummary($inventory): array
    {
        $summary = [
            'total_products' => $inventory->count(),
            'total_stock_value' => 0,
            'low_stock_count' => 0,
            'out_of_stock_count' => 0,
        ];

        foreach ($inventory as $item) {
            $summary['total_stock_value'] += $item->quantity * $item->product->cost_price;
            if ($item->quantity <= $item->min_stock_level) $summary['low_stock_count']++;
            if ($item->quantity == 0) $summary['out_of_stock_count']++;
        }

        return $summary;
    }
}
