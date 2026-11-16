<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Payment;
use App\Models\SubOrder;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class VendorOrderFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_vendor_can_complete_the_minimum_dummy_fulfilment_flow(): void
    {
        [$vendorUser, $customer, $vendor, $order, $subOrder] = $this->orderFixture();
        Payment::create(['payment_number' => 'PAY-TEST-1', 'order_id' => $order->id, 'user_id' => $customer->id, 'amount' => 500,
            'currency' => 'INR', 'payment_method' => 'cod', 'status' => 'pending']);
        Sanctum::actingAs($vendorUser);

        $this->patchJson("/api/vendor/orders/{$subOrder->id}/status", ['status' => 'processing'])
            ->assertOk()->assertJsonPath('data.order_status', 'processing');

        $shipmentId = $this->postJson("/api/vendor/orders/{$subOrder->id}/shipment", [
            'weight' => 1.5, 'length' => 20, 'width' => 15, 'height' => 10,
        ])->assertCreated()->assertJsonPath('data.provider', 'dummy')->json('data.id');

        foreach (['picked_up', 'in_transit', 'out_for_delivery', 'delivered'] as $status) {
            $this->patchJson("/api/vendor/shipments/{$shipmentId}/status", ['status' => $status])
                ->assertOk()->assertJsonPath('data.status', $status);
        }

        $this->assertDatabaseHas('sub_orders', ['id' => $subOrder->id, 'order_status' => 'delivered']);
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'order_status' => 'delivered', 'payment_status' => 'paid']);
        $this->assertDatabaseHas('payments', ['order_id' => $order->id, 'status' => 'paid']);
        $this->assertDatabaseCount('shipment_tracking', 5);
    }

    public function test_vendor_cannot_access_another_vendors_order(): void
    {
        [$vendorUser, , , , $subOrder] = $this->orderFixture();
        $otherUser = $this->user('other-vendor@example.com');
        Vendor::create(['user_id' => $otherUser->id, 'store_name' => 'Other Store', 'store_slug' => 'other-store', 'business_name' => 'Other Store', 'status' => 'approved']);
        Sanctum::actingAs($otherUser);

        $this->getJson("/api/vendor/orders/{$subOrder->id}")->assertNotFound();
    }

    private function orderFixture(): array
    {
        $vendorUser = $this->user('vendor@example.com');
        $customer = $this->user('customer@example.com');
        $vendor = Vendor::create(['user_id' => $vendorUser->id, 'store_name' => 'Craft Store', 'store_slug' => 'craft-store', 'business_name' => 'Craft Store', 'status' => 'approved']);
        $order = Order::create([
            'order_number' => 'MTA-TEST-1', 'user_id' => $customer->id, 'shipping_full_name' => 'Test Customer', 'shipping_phone' => '9999999999',
            'shipping_address_line_1' => 'Test Street', 'shipping_city' => 'Jaipur', 'shipping_state' => 'Rajasthan', 'shipping_country' => 'India',
            'shipping_postal_code' => '302001', 'subtotal' => 451, 'shipping_amount' => 49, 'total_amount' => 500, 'payment_status' => 'pending', 'order_status' => 'confirmed',
        ]);
        $subOrder = SubOrder::create(['sub_order_number' => 'MTA-TEST-1-1', 'order_id' => $order->id, 'vendor_id' => $vendor->id,
            'subtotal' => 451, 'total_amount' => 451, 'order_status' => 'confirmed', 'confirmed_at' => now()]);

        return [$vendorUser, $customer, $vendor, $order, $subOrder];
    }

    private function user(string $email): User
    {
        return User::create(['first_name' => 'Test', 'last_name' => 'User', 'email' => $email, 'password' => 'password', 'status' => 'active']);
    }
}
