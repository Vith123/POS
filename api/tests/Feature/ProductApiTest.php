<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $role = Role::create([
            'name' => 'admin',
            'permissions' => ['manage_products'],
        ]);

        return User::create([
            'name' => 'Admin',
            'email' => 'admin@pos.test',
            'password' => 'password',
            'role_id' => $role->id,
            'is_active' => true,
        ]);
    }

    private function category(): Category
    {
        return Category::create([
            'name' => 'Electronics',
            'description' => 'Devices',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_create_list_update_and_delete_a_product(): void
    {
        Storage::fake('public');

        $admin = $this->admin();
        $category = $this->category();

        $create = $this->actingAs($admin, 'sanctum')->post('/api/products', [
            'category_id' => $category->id,
            'name' => 'Wireless Mouse',
            'sku' => 'MOU-001',
            'barcode' => '123456',
            'description' => 'Ergonomic mouse',
            'price' => 19.5,
            'cost' => 8,
            'stock_quantity' => 12,
            'min_stock_level' => 3,
            'is_active' => '1',
            'image' => UploadedFile::fake()->image('mouse.jpg'),
        ]);

        $create->assertCreated()
            ->assertJsonPath('data.name', 'Wireless Mouse')
            ->assertJsonPath('data.price', 19.5)
            ->assertJsonPath('data.cost', 8)
            ->assertJsonPath('data.stock_quantity', 12)
            ->assertJsonPath('data.category.name', 'Electronics');

        $productId = $create->json('data.id');
        $this->assertNotNull(Product::find($productId)->image);
        Storage::disk('public')->assertExists(Product::find($productId)->image);

        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/products?search=Mouse&stock=low')
            ->assertOk()
            ->assertJsonCount(0, 'data');

        $this->actingAs($admin, 'sanctum')
            ->post('/api/products/' . $productId, [
                'name' => 'Wireless Mouse Pro',
                'price' => 25,
                'stock_quantity' => 0,
                'is_active' => '0',
            ])
            ->assertOk()
            ->assertJsonPath('data.name', 'Wireless Mouse Pro')
            ->assertJsonPath('data.price', 25)
            ->assertJsonPath('data.stock_quantity', 0)
            ->assertJsonPath('data.is_active', false);

        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/products?stock=out')
            ->assertOk()
            ->assertJsonPath('data.0.sku', 'MOU-001');

        $this->actingAs($admin, 'sanctum')
            ->deleteJson('/api/products/' . $productId)
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('products', ['id' => $productId]);
    }

    public function test_duplicate_sku_is_rejected(): void
    {
        $admin = $this->admin();
        $category = $this->category();

        $payload = [
            'category_id' => $category->id,
            'name' => 'Cable',
            'sku' => 'CBL-001',
            'price' => 5,
            'stock_quantity' => 1,
        ];

        $this->actingAs($admin, 'sanctum')->postJson('/api/products', $payload)->assertCreated();
        $this->actingAs($admin, 'sanctum')->postJson('/api/products', $payload)->assertStatus(422);
    }
}
