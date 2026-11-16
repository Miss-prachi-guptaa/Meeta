<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\SubOrder;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CustomerOrderDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_receives_structured_order_list_and_complete_owned_detail(): void
    {
        [$customer, $order] = $this->orderFixture();
        Sanctum::actingAs($customer);

        $this->getJson('/api/customer/orders')->assertOk()
            ->assertJsonPath('data.orders.0.id', $order->id)
            ->assertJsonPath('data.orders.0.total_items', 0)
            ->assertJsonStructure(['data' => ['orders', 'pagination']]);

        $this->getJson("/api/customer/orders/{$order->id}")->assertOk()
            ->assertJsonPath('data.delivery_address.city', 'Jaipur')
            ->assertJsonPath('data.amounts.total', 500)
            ->assertJsonPath('data.packages.0.vendor.name', 'Craft Store')
            ->assertJsonPath('data.can_cancel', true);
    }

    public function test_customer_cannot_view_another_customers_order(): void
    {
        [, $order] = $this->orderFixture();
        Sanctum::actingAs($this->user('other-customer@example.com'));

        $this->getJson("/api/customer/orders/{$order->id}")->assertNotFound();
    }

    private function orderFixture(): array
    {
        $customer = $this->user('customer-detail@example.com');
        $vendorUser = $this->user('vendor-detail@example.com');
        $vendor = Vendor::create(['user_id' => $vendorUser->id, 'store_name' => 'Craft Store', 'store_slug' => 'detail-craft-store', 'business_name' => 'Craft Store', 'status' => 'approved']);
        $order = Order::create(['order_number' => 'MTA-DETAIL-1', 'user_id' => $customer->id, 'shipping_full_name' => 'Asha Sharma',
            'shipping_phone' => '9999999999', 'shipping_address_line_1' => '12 Craft Lane', 'shipping_city' => 'Jaipur', 'shipping_state' => 'Rajasthan',
            'shipping_country' => 'India', 'shipping_postal_code' => '302001', 'subtotal' => 451, 'shipping_amount' => 49, 'total_amount' => 500,
            'payment_status' => 'pending', 'order_status' => 'confirmed']);
        SubOrder::create(['sub_order_number' => 'MTA-DETAIL-1-1', 'order_id' => $order->id, 'vendor_id' => $vendor->id,
            'subtotal' => 451, 'total_amount' => 451, 'order_status' => 'confirmed', 'confirmed_at' => now()]);

        return [$customer, $order];
    }

    private function user(string $email): User
    {
        return User::create(['first_name' => 'Test', 'last_name' => 'User', 'email' => $email, 'password' => 'password', 'status' => 'active']);
    }
}
