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
                'tax_percentage' => $data['tax_rate'] ?? 0,
                'tax_amount' => $taxAmount,
                'discount_type' => $data['discount_type'] ?? null,
                'discount_value' => $data['discount_value'] ?? 0,
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
