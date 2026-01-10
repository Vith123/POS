<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Category;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. CREATE ROLES
        $adminRole = Role::create([
            'name' => 'admin',
            'permissions' => [
                'manage_products',
                'manage_users',
                'manage_inventory',
                'view_reports',
                'process_sales',
            ],
        ]);

        $cashierRole = Role::create([
            'name' => 'cashier',
            'permissions' => [
                'process_sales',
                'view_products',
            ],
        ]);

        // 2. CREATE USERS
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@pos.com',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'is_active' => true,
        ]);

        User::create([
            'name' => 'John Cashier',
            'email' => 'cashier@pos.com',
            'password' => Hash::make('password'),
            'role_id' => $cashierRole->id,
            'is_active' => true,
        ]);

        // 3. CREATE CATEGORIES
        $electronics = Category::create([
            'name' => 'Electronics',
            'description' => 'Electronic devices and accessories',
            'is_active' => true,
        ]);

        $clothing = Category::create([
            'name' => 'Clothing',
            'description' => 'Apparel and fashion items',
            'is_active' => true,
        ]);

        $food = Category::create([
            'name' => 'Food & Beverages',
            'description' => 'Food items and drinks',
            'is_active' => true,
        ]);

        // 4. CREATE PRODUCTS WITH INVENTORY

        // Electronics Products
        $laptop = Product::create([
            'category_id' => $electronics->id,
            'name' => 'MacBook Pro 16"',
            'barcode' => '0194252456937',
            'sku' => 'LAP-MAC-16-001',
            'description' => 'Apple MacBook Pro 16-inch with M2 chip',
            'cost_price' => 1800.00,
            'selling_price' => 2499.00,
            'is_active' => true,
        ]);

        Inventory::create([
            'product_id' => $laptop->id,
            'quantity' => 15,
            'min_stock_level' => 5,
        ]);

        $mouse = Product::create([
            'category_id' => $electronics->id,
            'name' => 'Logitech MX Master 3',
            'barcode' => '097855148354',
            'sku' => 'MOU-LOG-MX3-001',
            'description' => 'Wireless ergonomic mouse',
            'cost_price' => 60.00,
            'selling_price' => 99.99,
            'is_active' => true,
        ]);

        Inventory::create([
            'product_id' => $mouse->id,
            'quantity' => 50,
            'min_stock_level' => 20,
        ]);

        $keyboard = Product::create([
            'category_id' => $electronics->id,
            'name' => 'Mechanical Keyboard RGB',
            'barcode' => '06939119035',
            'sku' => 'KEY-MEC-RGB-001',
            'description' => 'Gaming mechanical keyboard with RGB lighting',
            'cost_price' => 45.00,
            'selling_price' => 79.99,
            'is_active' => true,
        ]);

        Inventory::create([
            'product_id' => $keyboard->id,
            'quantity' => 30,
            'min_stock_level' => 15,
        ]);

        // Clothing Products
        $tshirt = Product::create([
            'category_id' => $clothing->id,
            'name' => 'Cotton T-Shirt',
            'barcode' => '074764002028',
            'sku' => 'CLO-TSH-COT-001',
            'description' => '100% cotton comfortable t-shirt',
            'cost_price' => 8.00,
            'selling_price' => 19.99,
            'is_active' => true,
        ]);

        Inventory::create([
            'product_id' => $tshirt->id,
            'quantity' => 100,
            'min_stock_level' => 30,
        ]);

        $jeans = Product::create([
            'category_id' => $clothing->id,
            'name' => 'Blue Jeans',
            'barcode' => '051497056865',
            'sku' => 'CLO-JEA-BLU-001',
            'description' => 'Classic blue denim jeans',
            'cost_price' => 25.00,
            'selling_price' => 49.99,
            'is_active' => true,
        ]);

        Inventory::create([
            'product_id' => $jeans->id,
            'quantity' => 60,
            'min_stock_level' => 20,
        ]);

        // Food Products
        $coffee = Product::create([
            'category_id' => $food->id,
            'name' => 'Premium Coffee Beans 1kg',
            'barcode' => '0025500001',
            'sku' => 'FOD-COF-PRE-001',
            'description' => 'Arabica coffee beans from Colombia',
            'cost_price' => 12.00,
            'selling_price' => 24.99,
            'is_active' => true,
        ]);

        Inventory::create([
            'product_id' => $coffee->id,
            'quantity' => 40,
            'min_stock_level' => 15,
        ]);

        $snacks = Product::create([
            'category_id' => $food->id,
            'name' => 'Mixed Nuts Pack',
            'barcode' => '0028400002',
            'sku' => 'FOD-SNA-NUT-001',
            'description' => 'Healthy mixed nuts 500g',
            'cost_price' => 5.00,
            'selling_price' => 12.99,
            'is_active' => true,
        ]);

        Inventory::create([
            'product_id' => $snacks->id,
            'quantity' => 80,
            'min_stock_level' => 25,
        ]);

        // Product with LOW STOCK (for demonstration)
        $headphones = Product::create([
            'category_id' => $electronics->id,
            'name' => 'Wireless Headphones',
            'barcode' => '0194252703021',
            'sku' => 'ELE-HEA-WIR-001',
            'description' => 'Noise cancelling wireless headphones',
            'cost_price' => 120.00,
            'selling_price' => 199.99,
            'is_active' => true,
        ]);

        Inventory::create([
            'product_id' => $headphones->id,
            'quantity' => 3, // LOW STOCK ALERT!
            'min_stock_level' => 10,
        ]);

        // 5. CREATE CUSTOMERS
        Customer::create([
            'name' => 'John Smith',
            'email' => 'john.smith@example.com',
            'phone' => '+1-555-0101',
            'address' => '123 Main Street, New York, NY 10001',
            'type' => 'regular',
            'credit_limit' => 1000.00,
            'credit_balance' => 0,
            'loyalty_points' => 250,
            'is_active' => true,
        ]);

        Customer::create([
            'name' => 'Sarah Johnson',
            'email' => 'sarah.j@example.com',
            'phone' => '+1-555-0102',
            'address' => '456 Oak Avenue, Los Angeles, CA 90001',
            'type' => 'vip',
            'credit_limit' => 5000.00,
            'credit_balance' => 0,
            'loyalty_points' => 1500,
            'is_active' => true,
        ]);

        Customer::create([
            'name' => 'Michael Brown',
            'email' => 'mbrown@example.com',
            'phone' => '+1-555-0103',
            'address' => '789 Pine Road, Chicago, IL 60601',
            'type' => 'regular',
            'credit_limit' => 500.00,
            'credit_balance' => 0,
            'loyalty_points' => 50,
            'is_active' => true,
        ]);

        Customer::create([
            'name' => 'Emily Davis',
            'email' => 'emily.davis@example.com',
            'phone' => '+1-555-0104',
            'address' => '321 Elm Street, Houston, TX 77001',
            'type' => 'vip',
            'credit_limit' => 10000.00,
            'credit_balance' => 0,
            'loyalty_points' => 3200,
            'is_active' => true,
        ]);

        Customer::create([
            'name' => 'David Wilson',
            'email' => null,
            'phone' => '+1-555-0105',
            'address' => null,
            'type' => 'regular',
            'credit_limit' => 0,
            'credit_balance' => 0,
            'loyalty_points' => 0,
            'is_active' => true,
        ]);

        echo "\n✅ Database seeded successfully!\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "👤 Admin Login:\n";
        echo "   Email: admin@pos.com\n";
        echo "   Password: password\n\n";
        echo "👤 Cashier Login:\n";
        echo "   Email: cashier@pos.com\n";
        echo "   Password: password\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "📊 Sample Data:\n";
        echo "   - 2 Roles (Admin, Cashier)\n";
        echo "   - 2 Users\n";
        echo "   - 3 Categories\n";
        echo "   - 8 Products with inventory\n";
        echo "   - 5 Customers (2 VIP, 3 Regular)\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    }
}
