<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Customer;

class CoffeeShopSeeder extends Seeder
{
    /**
     * Seed the coffee shop with realistic data.
     */
    public function run(): void
    {
        // Create Categories
        $categories = [
            ['name' => 'Hot Coffee', 'description' => 'Hot brewed coffee drinks', 'is_active' => true],
            ['name' => 'Iced Coffee', 'description' => 'Cold coffee beverages', 'is_active' => true],
            ['name' => 'Espresso Drinks', 'description' => 'Espresso based beverages', 'is_active' => true],
            ['name' => 'Tea & Others', 'description' => 'Tea and alternative drinks', 'is_active' => true],
            ['name' => 'Pastries', 'description' => 'Fresh baked goods', 'is_active' => true],
            ['name' => 'Snacks', 'description' => 'Light snacks and treats', 'is_active' => true],
        ];

        foreach ($categories as $categoryData) {
            Category::create($categoryData);
        }

        // Get category IDs
        $hotCoffee = Category::where('name', 'Hot Coffee')->first()->id;
        $icedCoffee = Category::where('name', 'Iced Coffee')->first()->id;
        $espresso = Category::where('name', 'Espresso Drinks')->first()->id;
        $teaOthers = Category::where('name', 'Tea & Others')->first()->id;
        $pastries = Category::where('name', 'Pastries')->first()->id;
        $snacks = Category::where('name', 'Snacks')->first()->id;

// Create Products (Note: products table uses cost_price and selling_price, not price and cost)
        $products = [
            // Hot Coffee
            ['name' => 'Americano', 'sku' => 'HOT-001', 'barcode' => '1001', 'category_id' => $hotCoffee, 'selling_price' => 3.50, 'cost_price' => 1.00, 'is_active' => true],
            ['name' => 'Cappuccino', 'sku' => 'HOT-002', 'barcode' => '1002', 'category_id' => $hotCoffee, 'selling_price' => 4.50, 'cost_price' => 1.20, 'is_active' => true],
            ['name' => 'Latte', 'sku' => 'HOT-003', 'barcode' => '1003', 'category_id' => $hotCoffee, 'selling_price' => 4.50, 'cost_price' => 1.20, 'is_active' => true],
            ['name' => 'Mocha', 'sku' => 'HOT-004', 'barcode' => '1004', 'category_id' => $hotCoffee, 'selling_price' => 5.00, 'cost_price' => 1.50, 'is_active' => true],
            ['name' => 'Flat White', 'sku' => 'HOT-005', 'barcode' => '1005', 'category_id' => $hotCoffee, 'selling_price' => 4.75, 'cost_price' => 1.30, 'is_active' => true],

            // Iced Coffee
            ['name' => 'Iced Americano', 'sku' => 'ICE-001', 'barcode' => '2001', 'category_id' => $icedCoffee, 'selling_price' => 4.00, 'cost_price' => 1.10, 'is_active' => true],
            ['name' => 'Iced Latte', 'sku' => 'ICE-002', 'barcode' => '2002', 'category_id' => $icedCoffee, 'selling_price' => 5.00, 'cost_price' => 1.40, 'is_active' => true],
            ['name' => 'Iced Mocha', 'sku' => 'ICE-003', 'barcode' => '2003', 'category_id' => $icedCoffee, 'selling_price' => 5.50, 'cost_price' => 1.60, 'is_active' => true],
            ['name' => 'Cold Brew', 'sku' => 'ICE-004', 'barcode' => '2004', 'category_id' => $icedCoffee, 'selling_price' => 4.50, 'cost_price' => 1.30, 'is_active' => true],
            ['name' => 'Iced Caramel Macchiato', 'sku' => 'ICE-005', 'barcode' => '2005', 'category_id' => $icedCoffee, 'selling_price' => 6.00, 'cost_price' => 1.80, 'is_active' => true],

            // Espresso Drinks
            ['name' => 'Espresso Single', 'sku' => 'ESP-001', 'barcode' => '3001', 'category_id' => $espresso, 'selling_price' => 2.50, 'cost_price' => 0.80, 'is_active' => true],
            ['name' => 'Espresso Double', 'sku' => 'ESP-002', 'barcode' => '3002', 'category_id' => $espresso, 'selling_price' => 3.50, 'cost_price' => 1.00, 'is_active' => true],
            ['name' => 'Macchiato', 'sku' => 'ESP-003', 'barcode' => '3003', 'category_id' => $espresso, 'selling_price' => 3.75, 'cost_price' => 1.10, 'is_active' => true],
            ['name' => 'Cortado', 'sku' => 'ESP-004', 'barcode' => '3004', 'category_id' => $espresso, 'selling_price' => 4.00, 'cost_price' => 1.20, 'is_active' => true],

            // Tea & Others
            ['name' => 'English Breakfast Tea', 'sku' => 'TEA-001', 'barcode' => '4001', 'category_id' => $teaOthers, 'selling_price' => 3.00, 'cost_price' => 0.80, 'is_active' => true],
            ['name' => 'Green Tea', 'sku' => 'TEA-002', 'barcode' => '4002', 'category_id' => $teaOthers, 'selling_price' => 3.00, 'cost_price' => 0.80, 'is_active' => true],
            ['name' => 'Chai Latte', 'sku' => 'TEA-003', 'barcode' => '4003', 'category_id' => $teaOthers, 'selling_price' => 4.50, 'cost_price' => 1.30, 'is_active' => true],
            ['name' => 'Hot Chocolate', 'sku' => 'HOT-006', 'barcode' => '4004', 'category_id' => $teaOthers, 'selling_price' => 4.00, 'cost_price' => 1.20, 'is_active' => true],

            // Pastries
            ['name' => 'Croissant', 'sku' => 'PAS-001', 'barcode' => '5001', 'category_id' => $pastries, 'selling_price' => 3.50, 'cost_price' => 1.00, 'is_active' => true],
            ['name' => 'Chocolate Muffin', 'sku' => 'PAS-002', 'barcode' => '5002', 'category_id' => $pastries, 'selling_price' => 4.00, 'cost_price' => 1.20, 'is_active' => true],
            ['name' => 'Blueberry Muffin', 'sku' => 'PAS-003', 'barcode' => '5003', 'category_id' => $pastries, 'selling_price' => 4.00, 'cost_price' => 1.20, 'is_active' => true],
            ['name' => 'Cinnamon Roll', 'sku' => 'PAS-004', 'barcode' => '5004', 'category_id' => $pastries, 'selling_price' => 4.50, 'cost_price' => 1.30, 'is_active' => true],
            ['name' => 'Bagel with Cream Cheese', 'sku' => 'PAS-005', 'barcode' => '5005', 'category_id' => $pastries, 'selling_price' => 5.00, 'cost_price' => 1.50, 'is_active' => true],

            // Snacks
            ['name' => 'Chocolate Chip Cookie', 'sku' => 'SNK-001', 'barcode' => '6001', 'category_id' => $snacks, 'selling_price' => 2.50, 'cost_price' => 0.70, 'is_active' => true],
            ['name' => 'Brownie', 'sku' => 'SNK-002', 'barcode' => '6002', 'category_id' => $snacks, 'selling_price' => 3.50, 'cost_price' => 1.00, 'is_active' => true],
            ['name' => 'Granola Bar', 'sku' => 'SNK-003', 'barcode' => '6003', 'category_id' => $snacks, 'selling_price' => 2.00, 'cost_price' => 0.60, 'is_active' => true],
            ['name' => 'Fruit Cup', 'sku' => 'SNK-004', 'barcode' => '6004', 'category_id' => $snacks, 'selling_price' => 4.50, 'cost_price' => 1.50, 'is_active' => true],
        ];

        foreach ($products as $productData) {
            $product = Product::create($productData);

            // Create inventory record for each product
            Inventory::create([
                'product_id' => $product->id,
                'quantity' => 100,
                'min_stock_level' => 20,
            ]);
        }

        // Create some sample customers
        $customers = [
            ['name' => 'John Smith', 'email' => 'john@example.com', 'phone' => '555-0101', 'address' => '123 Main St', 'loyalty_points' => 150, 'is_active' => true],
            ['name' => 'Emma Wilson', 'email' => 'emma@example.com', 'phone' => '555-0102', 'address' => '456 Oak Ave', 'loyalty_points' => 230, 'is_active' => true],
            ['name' => 'Michael Brown', 'email' => 'michael@example.com', 'phone' => '555-0103', 'address' => '789 Pine Rd', 'loyalty_points' => 85, 'is_active' => true],
            ['name' => 'Sarah Davis', 'email' => 'sarah@example.com', 'phone' => '555-0104', 'address' => '321 Elm St', 'loyalty_points' => 420, 'is_active' => true],
            ['name' => 'James Johnson', 'email' => 'james@example.com', 'phone' => '555-0105', 'address' => '654 Maple Dr', 'loyalty_points' => 95, 'is_active' => true],
        ];

        foreach ($customers as $customerData) {
            Customer::create($customerData);
        }

        $this->command->info('☕ Coffee shop data seeded successfully!');
        $this->command->info('📦 Created ' . count($products) . ' products');
        $this->command->info('📂 Created ' . count($categories) . ' categories');
        $this->command->info('👥 Created ' . count($customers) . ' customers');
    }
}
