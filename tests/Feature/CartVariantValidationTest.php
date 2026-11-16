<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use Database\Seeders\DemoCatalogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartVariantValidationTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $owner = User::create(['first_name' => 'Demo', 'last_name' => 'Vendor', 'email' => 'cart-vendor@example.com', 'password' => 'password', 'status' => 'active']);
        Vendor::create(['user_id' => $owner->id, 'store_name' => 'Test Store', 'store_slug' => 'test-store', 'business_name' => 'Test Store', 'status' => 'approved']);
        $this->customer = User::create(['first_name' => 'Cart', 'last_name' => 'Customer', 'email' => 'cart-customer@example.com', 'password' => 'password', 'status' => 'active']);
        $this->seed(DemoCatalogSeeder::class);
    }

    public function test_variant_selection_is_required_to_add_an_item(): void
    {
        $this->actingAs($this->customer)->postJson('/api/customer/cart/items', ['quantity' => 1])
            ->assertUnprocessable()
            ->assertJsonPath('errors.product_variant_id.0', 'Please select the required product options before adding this item to your cart.');
    }

    public function test_unknown_or_inactive_variant_returns_a_clear_validation_error(): void
    {
        $this->actingAs($this->customer)->postJson('/api/customer/cart/items', ['product_variant_id' => 999999, 'quantity' => 1])
            ->assertUnprocessable()
            ->assertJsonPath('errors.product_variant_id.0', 'The selected product option is unavailable. Please choose another option.');

        $variant = Product::where('slug', 'jaipur-blue-pottery-floral-vase')->firstOrFail()->variants()->firstOrFail();
        $variant->update(['is_active' => false]);

        $this->actingAs($this->customer)->postJson('/api/customer/cart/items', ['product_variant_id' => $variant->id, 'quantity' => 1])
            ->assertUnprocessable()
            ->assertJsonPath('errors.product_variant_id.0', 'The selected product option is unavailable. Please choose another option.');
    }

    public function test_out_of_stock_variant_cannot_be_added(): void
    {
        $variant = Product::where('slug', 'jaipur-blue-pottery-floral-vase')->firstOrFail()->variants()->firstOrFail();
        $variant->inventory()->update(['quantity' => 0, 'reserved_quantity' => 0]);

        $this->actingAs($this->customer)->postJson('/api/customer/cart/items', ['product_variant_id' => $variant->id, 'quantity' => 1])
            ->assertUnprocessable()
            ->assertJsonPath('errors.product_variant_id.0', 'The selected product option is out of stock. Please choose another option.');
    }

    public function test_buy_now_checks_out_only_selected_variant_and_preserves_existing_cart(): void
    {
        $variants = Product::where('slug', 'jaipur-blue-pottery-floral-vase')->firstOrFail()->variants()->take(2)->get();
        $cartVariant = $variants->first();
        $buyNowVariant = $variants->last();

        $this->actingAs($this->customer)->postJson('/api/customer/cart/items', [
            'product_variant_id' => $cartVariant->id, 'quantity' => 1,
        ])->assertCreated();

        $this->actingAs($this->customer)->getJson('/api/customer/checkout?mode=buy_now&product_variant_id='.$buyNowVariant->id.'&quantity=1&shipping_method=standard')
            ->assertOk()
            ->assertJsonPath('data.mode', 'buy_now')
            ->assertJsonCount(1, 'data.items')
            ->assertJsonPath('data.items.0.variant_id', $buyNowVariant->id);

        $address = $this->customer->addresses()->create([
            'label' => 'Home', 'full_name' => 'Cart Customer', 'phone' => '9876543210',
            'address_line_1' => '20 MG Road', 'city' => 'Jaipur', 'state' => 'Rajasthan',
            'country' => 'India', 'postal_code' => '302001', 'is_default' => true,
        ]);

        $this->actingAs($this->customer)->postJson('/api/customer/checkout/place-order', [
            'mode' => 'buy_now', 'product_variant_id' => $buyNowVariant->id, 'quantity' => 1,
            'address_id' => $address->id, 'shipping_method' => 'standard', 'payment_method' => 'cod',
        ])->assertCreated();

        $this->assertDatabaseHas('cart_items', ['product_variant_id' => $cartVariant->id, 'quantity' => 1]);
        $this->assertDatabaseHas('order_items', ['product_variant_id' => $buyNowVariant->id, 'quantity' => 1]);
        $this->assertDatabaseMissing('order_items', ['product_variant_id' => $cartVariant->id]);
    }
}
