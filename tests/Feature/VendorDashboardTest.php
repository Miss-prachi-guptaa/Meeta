<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\SubOrder;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class VendorDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_returns_vendor_scoped_commerce_metrics(): void
    {
        $vendorUser = $this->user('vendor-dashboard@example.com');
        $otherUser = $this->user('other-dashboard@example.com');
        $customer = $this->user('dashboard-customer@example.com');
        $vendor = $this->vendor($vendorUser, 'Dashboard Store');
        $otherVendor = $this->vendor($otherUser, 'Other Store');
        $category = Category::create(['name' => 'Vases', 'slug' => 'dashboard-vases']);

        Product::create(['vendor_id' => $vendor->id, 'category_id' => $category->id, 'name' => 'Active Vase', 'slug' => 'dashboard-active-vase', 'status' => 'active']);
        Product::create(['vendor_id' => $vendor->id, 'category_id' => $category->id, 'name' => 'Pending Vase', 'slug' => 'dashboard-pending-vase', 'status' => 'pending']);
        Product::create(['vendor_id' => $otherVendor->id, 'category_id' => $category->id, 'name' => 'Other Vase', 'slug' => 'dashboard-other-vase', 'status' => 'active']);

        $order = Order::create([
            'order_number' => 'MTA-DASH-1', 'user_id' => $customer->id, 'shipping_full_name' => 'Asha Sharma',
            'shipping_phone' => '9999999999', 'shipping_address_line_1' => 'Craft Road', 'shipping_city' => 'Jaipur',
            'shipping_state' => 'Rajasthan', 'shipping_country' => 'India', 'shipping_postal_code' => '302001',
            'subtotal' => 1200, 'total_amount' => 1200, 'payment_status' => 'paid', 'order_status' => 'confirmed',
        ]);
        SubOrder::create(['sub_order_number' => 'MTA-DASH-1-1', 'order_id' => $order->id, 'vendor_id' => $vendor->id, 'subtotal' => 1200, 'total_amount' => 1200, 'order_status' => 'confirmed']);
        SubOrder::create(['sub_order_number' => 'MTA-DASH-1-2', 'order_id' => $order->id, 'vendor_id' => $otherVendor->id, 'subtotal' => 800, 'total_amount' => 800, 'order_status' => 'confirmed']);

        Sanctum::actingAs($vendorUser);

        $this->getJson('/api/vendor/dashboard?range=week')
            ->assertOk()
            ->assertJsonPath('message', 'Vendor dashboard fetched successfully.')
            ->assertJsonPath('data.vendor.id', $vendor->id)
            ->assertJsonPath('data.summary.total_products', 2)
            ->assertJsonPath('data.summary.total_orders', 1)
            ->assertJsonPath('data.summary.period_sales', 1200)
            ->assertJsonPath('data.product_status_counts.active', 1)
            ->assertJsonPath('data.product_status_counts.pending', 1)
            ->assertJsonPath('data.attention.new_orders', 1)
            ->assertJsonCount(1, 'data.recent_orders');
    }

    public function test_dashboard_rejects_invalid_range(): void
    {
        $user = $this->user('range-vendor@example.com');
        $this->vendor($user, 'Range Store');
        Sanctum::actingAs($user);

        $this->getJson('/api/vendor/dashboard?range=year')->assertUnprocessable();
    }

    public function test_unapproved_vendor_cannot_access_dashboard(): void
    {
        $user = $this->user('pending-dashboard@example.com');
        Vendor::create(['user_id' => $user->id, 'store_name' => 'Pending Store', 'store_slug' => 'pending-store', 'business_name' => 'Pending Store', 'status' => 'pending']);
        Sanctum::actingAs($user);

        $this->getJson('/api/vendor/dashboard')->assertUnprocessable()
            ->assertJsonValidationErrors('vendor');
    }

    private function user(string $email): User
    {
        return User::create(['first_name' => 'Test', 'last_name' => 'User', 'email' => $email, 'password' => 'password', 'status' => 'active']);
    }

    private function vendor(User $user, string $store): Vendor
    {
        return Vendor::create([
            'user_id' => $user->id,
            'store_name' => $store,
            'store_slug' => str($store)->slug(),
            'business_name' => $store,
            'status' => 'approved',
        ]);
    }
}
