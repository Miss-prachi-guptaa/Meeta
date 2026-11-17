<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\SubOrder;
use App\Models\User;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminOrderMonitoringTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    }

    public function test_admin_can_fetch_orders_with_sla_monitoring()
    {
        $admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin-test@example.com',
            'password' => 'password',
            'status' => 'active',
        ]);
        $admin->assignRole('admin');

        $vendorUser = User::create([
            'first_name' => 'Vendor',
            'last_name' => 'Owner',
            'email' => 'vendor-test@example.com',
            'password' => 'password',
            'status' => 'active',
        ]);
        $vendor = Vendor::create([
            'user_id' => $vendorUser->id,
            'store_name' => 'Test Vendor',
            'store_slug' => 'test-vendor',
            'business_name' => 'Test Vendor Pvt',
            'status' => 'approved',
        ]);

        $order = Order::create([
            'order_number' => 'ORD-TEST-001',
            'user_id' => $admin->id,
            'shipping_full_name' => 'John Doe',
            'shipping_phone' => '9876543210',
            'shipping_address_line_1' => 'Street 1',
            'shipping_city' => 'Delhi',
            'shipping_state' => 'Delhi',
            'shipping_country' => 'India',
            'shipping_postal_code' => '110001',
            'subtotal' => 1000,
            'total_amount' => 1000,
            'payment_status' => 'paid',
            'order_status' => 'processing',
        ]);

        $subOrder = SubOrder::create([
            'sub_order_number' => 'SUB-TEST-001',
            'order_id' => $order->id,
            'vendor_id' => $vendor->id,
            'subtotal' => 1000,
            'total_amount' => 1000,
            'order_status' => 'confirmed',
        ]);
        $subOrder->created_at = Carbon::now()->subHours(25);
        $subOrder->save();

        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/admin/orders');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'stats' => ['total_orders', 'total_sub_orders', 'pending_confirmation', 'sla_breached_count'],
                'orders' => ['data'],
            ],
        ]);

        $this->assertEquals(1, $response->json('data.stats.sla_breached_count'));
    }

    public function test_admin_can_auto_cancel_sla_breached_sub_order()
    {
        $admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin-test2@example.com',
            'password' => 'password',
            'status' => 'active',
        ]);
        $admin->assignRole('admin');

        $vendorUser = User::create([
            'first_name' => 'Vendor2',
            'last_name' => 'Owner',
            'email' => 'vendor-test2@example.com',
            'password' => 'password',
            'status' => 'active',
        ]);
        $vendor = Vendor::create([
            'user_id' => $vendorUser->id,
            'store_name' => 'Test Vendor 2',
            'store_slug' => 'test-vendor-2',
            'business_name' => 'Test Vendor 2 Pvt',
            'status' => 'approved',
        ]);

        $order = Order::create([
            'order_number' => 'ORD-TEST-002',
            'user_id' => $admin->id,
            'shipping_full_name' => 'Jane Doe',
            'shipping_phone' => '9876543211',
            'shipping_address_line_1' => 'Street 2',
            'shipping_city' => 'Mumbai',
            'shipping_state' => 'Maharashtra',
            'shipping_country' => 'India',
            'shipping_postal_code' => '400001',
            'subtotal' => 2000,
            'total_amount' => 2000,
            'payment_status' => 'paid',
            'order_status' => 'processing',
        ]);

        $subOrder = SubOrder::create([
            'sub_order_number' => 'SUB-TEST-002',
            'order_id' => $order->id,
            'vendor_id' => $vendor->id,
            'subtotal' => 2000,
            'total_amount' => 2000,
            'order_status' => 'confirmed',
            'created_at' => Carbon::now()->subHours(26),
        ]);

        Sanctum::actingAs($admin);

        $response = $this->postJson("/api/admin/sub-orders/{$subOrder->id}/auto-cancel", [
            'reason' => 'Vendor unconfirmed for over 24 hours.',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('sub_orders', [
            'id' => $subOrder->id,
            'order_status' => 'cancelled',
        ]);
    }
}
