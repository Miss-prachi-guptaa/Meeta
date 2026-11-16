<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vendor;
use Database\Seeders\DemoCatalogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductVariantSelectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeded_product_exposes_frontend_ready_option_groups_and_combinations(): void
    {
        $user = User::create(['first_name' => 'Demo', 'last_name' => 'Vendor', 'email' => 'catalog-vendor@example.com', 'password' => 'password', 'status' => 'active']);
        Vendor::create(['user_id' => $user->id, 'store_name' => 'Test Store', 'store_slug' => 'test-store', 'business_name' => 'Test Store', 'status' => 'approved']);
        $this->seed(DemoCatalogSeeder::class);

        $this->getJson('/api/customer/products/jaipur-blue-pottery-floral-vase')->assertOk()
            ->assertJsonPath('data.option_groups.0.name', 'Color')
            ->assertJsonPath('data.option_groups.1.name', 'Size')
            ->assertJsonCount(4, 'data.variants')
            ->assertJsonPath('data.variants.0.attributes.0.attribute_slug', 'color')
            ->assertJsonStructure(['data' => ['variants' => [['id', 'sku', 'effective_price', 'in_stock', 'available_quantity', 'attributes']], 'option_groups']]);
    }
}
