#!/bin/bash
echo "🚀 Creating all Service classes..."

# Create SaleService
cat > app/Services/SaleService.php << 'EOPHP'
<?php
namespace App\Services;

use App\Models\Sale;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;

class SaleService
{
    public function createSale(array $data, int $userId): Sale
    {
        return DB::transaction(function () use ($data, $userId) {
            // Calculate totals
            $subtotal = 0;
            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $subtotal += $product->selling_price * $item['quantity'];
            }

            // Calculate tax and discount
            $taxAmount = $data['tax_rate'] ? ($subtotal * $data['tax_rate'] / 100) : 0;
            $discountAmount = $this->calculateDiscount($subtotal, $data);
            $totalAmount = $subtotal + $taxAmount - $discountAmount;

            // Create sale
            $sale = Sale::create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'user_id' => $userId,
                'customer_id' => $data['customer_id'] ?? null,
                'subtotal' => $subtotal,
                'tax_rate' => $data['tax_rate'] ?? 0,
                'tax_amount' => $taxAmount,
                'discount_type' => $data['discount_type'] ?? null,
                'discount_value' => $data['discount_value'] ?? 0,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'status' => 'completed',
            ]);

            // Create sale items and deduct inventory
            foreach ($data['items'] as $itemData) {
                $product = Product::findOrFail($itemData['product_id']);
                
                $sale->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $product->selling_price,
                    'subtotal' => $product->selling_price * $itemData['quantity'],
                ]);

                // Deduct inventory
                $inventory = Inventory::where('product_id', $product->id)->first();
                $inventory->decrement('quantity', $itemData['quantity']);

                // Record stock movement
                StockMovement::create([
                    'product_id' => $product->id,
                    'type' => 'out',
                    'quantity' => $itemData['quantity'],
                    'reference' => "Sale #{$sale->invoice_number}",
                    'user_id' => $userId,
                ]);
            }

            // Create payments
            if (!empty($data['payments'])) {
                foreach ($data['payments'] as $payment) {
                    $sale->payments()->create($payment);
                }
            }

            return $sale->load(['items.product', 'payments']);
        });
    }

    private function calculateDiscount(float $subtotal, array $data): float
    {
        if (empty($data['discount_type'])) return 0;

        if ($data['discount_type'] === 'percentage') {
            return ($subtotal * $data['discount_value']) / 100;
        }
        
        return $data['discount_value'];
    }

    private function generateInvoiceNumber(): string
    {
        return 'INV-' . date('Ymd') . '-' . str_pad(Sale::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
    }
}
EOPHP

# Create CustomerService
cat > app/Services/CustomerService.php << 'EOPHP'
<?php
namespace App\Services;

use App\Models\Customer;

class CustomerService
{
    public function getAll(array $filters, int $perPage = 15)
    {
        $query = Customer::query();

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getById(int $id): ?Customer
    {
        return Customer::with(['sales' => function($query) {
            $query->orderBy('created_at', 'desc')->limit(10);
        }])->find($id);
    }

    public function create(array $data): Customer
    {
        return Customer::create($data);
    }

    public function update(int $id, array $data): Customer
    {
        $customer = Customer::findOrFail($id);
        $customer->update($data);
        return $customer->fresh();
    }

    public function delete(int $id): bool
    {
        return Customer::findOrFail($id)->delete();
    }

    public function manageLoyaltyPoints(int $id, string $action, int $points): Customer
    {
        $customer = Customer::findOrFail($id);

        if ($action === 'add') {
            $customer->increment('loyalty_points', $points);
        } else {
            $customer->decrement('loyalty_points', $points);
        }

        return $customer->fresh();
    }
}
EOPHP

# Create InventoryService
cat > app/Services/InventoryService.php << 'EOPHP'
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
EOPHP

# Create ReturnService
cat > app/Services/ReturnService.php << 'EOPHP'
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
EOPHP

echo "✅ All Service classes created successfully!"
ls -la app/Services/
