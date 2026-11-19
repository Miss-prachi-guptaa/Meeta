<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Brand;
use App\Models\CommissionRule;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\ReturnModel;
use App\Models\ReturnItem;
use App\Models\SubOrder;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorBankAccount;
use App\Models\VendorLedger;
use App\Models\VendorPayout;
use App\Models\VendorSettlement;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class RealisticMarketplaceSampleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'vendor']);
        Role::firstOrCreate(['name' => 'customer']);

        // 2. Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@meeta.in'],
            [
                'first_name' => 'Meeta',
                'last_name' => 'Admin',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );
        $admin->assignRole('admin');

        // 3. Customers
        $customer1 = User::firstOrCreate(
            ['email' => 'rohan.sharma@example.com'],
            [
                'first_name' => 'Rohan',
                'last_name' => 'Sharma',
                'phone' => '9811223344',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );
        $customer1->assignRole('customer');

        $customer2 = User::firstOrCreate(
            ['email' => 'priya.verma@example.com'],
            [
                'first_name' => 'Priya',
                'last_name' => 'Verma',
                'phone' => '9877665544',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );
        $customer2->assignRole('customer');

        // 4. Vendors
        $vendorUser1 = User::firstOrCreate(
            ['email' => 'urbanthreads@example.com'],
            [
                'first_name' => 'Rajesh',
                'last_name' => 'Kumar',
                'phone' => '9988776655',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );
        $vendorUser1->assignRole('vendor');

        $vendor1 = Vendor::firstOrCreate(
            ['user_id' => $vendorUser1->id],
            [
                'store_name' => 'Urban Threads India',
                'store_slug' => 'urban-threads',
                'business_name' => 'Urban Threads Textiles Pvt Ltd',
                'support_email' => 'urbanthreads@example.com',
                'support_phone' => '9988776655',
                'status' => 'approved',
                'submitted_at' => Carbon::now()->subDays(30),
            ]
        );

        VendorBankAccount::firstOrCreate(
            ['vendor_id' => $vendor1->id],
            [
                'bank_name' => 'HDFC Bank',
                'account_number' => '50100482910293',
                'ifsc_code' => 'HDFC0000128',
                'account_holder_name' => 'Urban Threads Textiles Pvt Ltd',
                'is_verified' => true,
            ]
        );

        $vendorUser2 = User::firstOrCreate(
            ['email' => 'claycraft@example.com'],
            [
                'first_name' => 'Ananya',
                'last_name' => 'Iyer',
                'phone' => '9822334455',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );
        $vendorUser2->assignRole('vendor');

        $vendor2 = Vendor::firstOrCreate(
            ['user_id' => $vendorUser2->id],
            [
                'store_name' => 'Clay & Terracotta Studio',
                'store_slug' => 'clay-terracotta-studio',
                'business_name' => 'Clay Creations LLP',
                'support_email' => 'claycraft@example.com',
                'support_phone' => '9822334455',
                'status' => 'approved',
                'submitted_at' => Carbon::now()->subDays(20),
            ]
        );

        VendorBankAccount::firstOrCreate(
            ['vendor_id' => $vendor2->id],
            [
                'bank_name' => 'ICICI Bank',
                'account_number' => '001205004819',
                'ifsc_code' => 'ICIC0000012',
                'account_holder_name' => 'Clay Creations LLP',
                'is_verified' => true,
            ]
        );

        // Vendor 3: Pending Approval Vendor
        $vendorUser3 = User::firstOrCreate(
            ['email' => 'artisanwood@example.com'],
            [
                'first_name' => 'Sunil',
                'last_name' => 'Mehta',
                'phone' => '9711223344',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );
        $vendorUser3->assignRole('vendor');

        Vendor::firstOrCreate(
            ['user_id' => $vendorUser3->id],
            [
                'store_name' => 'Artisan Wood & Metal',
                'store_slug' => 'artisan-wood-metal',
                'business_name' => 'Artisan Handcrafts Ltd',
                'support_email' => 'artisanwood@example.com',
                'support_phone' => '9711223344',
                'status' => 'pending',
                'submitted_at' => Carbon::now()->subDays(1),
            ]
        );

        // 5. Categories & Commission Rules
        $catFashion = Category::firstOrCreate(['slug' => 'handcrafted-fashion'], ['name' => 'Handcrafted Fashion', 'is_active' => true]);
        $catDecor = Category::firstOrCreate(['slug' => 'home-pottery-decor'], ['name' => 'Home & Pottery Decor', 'is_active' => true]);

        CommissionRule::updateOrCreate(
            ['category_id' => null, 'vendor_id' => null],
            ['title' => 'Global Fallback Commission', 'commission_rate' => 10.00, 'type' => 'percentage', 'is_active' => true]
        );

        CommissionRule::updateOrCreate(
            ['category_id' => $catFashion->id],
            ['title' => 'Fashion Standard Rate', 'commission_rate' => 12.50, 'type' => 'percentage', 'is_active' => true]
        );

        CommissionRule::updateOrCreate(
            ['category_id' => $catDecor->id],
            ['title' => 'Pottery & Fragile Goods Rate', 'commission_rate' => 8.00, 'type' => 'percentage', 'is_active' => true]
        );

        // 6. Products
        $brand = Brand::firstOrCreate(['slug' => 'meeta-select'], ['name' => 'Meeta Select', 'is_active' => true]);

        $product1 = Product::firstOrCreate(
            ['slug' => 'indigo-block-print-kurta'],
            [
                'vendor_id' => $vendor1->id,
                'category_id' => $catFashion->id,
                'brand_id' => $brand->id,
                'name' => 'Hand Block Printed Indigo Cotton Kurta',
                'status' => 'active',
                'description' => 'Authentic hand block printed cotton kurta from Bagru artisans.',
            ]
        );

        $variant1 = ProductVariant::firstOrCreate(
            ['product_id' => $product1->id, 'sku' => 'UT-KURTA-L'],
            [
                'name' => 'Size: L, Color: Indigo Blue',
                'price' => 1899.00,
                'is_active' => true,
            ]
        );

        Inventory::updateOrCreate(
            ['product_variant_id' => $variant1->id],
            ['quantity' => 45, 'minimum_stock' => 5]
        );

        ProductImage::firstOrCreate(
            ['product_id' => $product1->id, 'image_url' => '/images/catalog/indigo-block-print-cushion-1.webp'],
            ['alt_text' => 'Hand Block Printed Indigo Kurta Fabric', 'is_primary' => true, 'sort_order' => 1]
        );

        $product2 = Product::firstOrCreate(
            ['slug' => 'terracotta-clay-diffuser-vase'],
            [
                'vendor_id' => $vendor2->id,
                'category_id' => $catDecor->id,
                'brand_id' => $brand->id,
                'name' => 'Handcrafted Terracotta Aroma Diffuser Vase',
                'status' => 'active',
                'description' => 'Eco-friendly natural clay diffuser crafted on potter wheel.',
            ]
        );

        $variant2 = ProductVariant::firstOrCreate(
            ['product_id' => $product2->id, 'sku' => 'CLAY-VASE-STD'],
            [
                'name' => 'Standard / Terracotta Brown',
                'price' => 950.00,
                'is_active' => true,
            ]
        );

        Inventory::updateOrCreate(
            ['product_variant_id' => $variant2->id],
            ['quantity' => 20, 'minimum_stock' => 2]
        );

        ProductImage::firstOrCreate(
            ['product_id' => $product2->id, 'image_url' => '/images/catalog/warli-terracotta-planter-1.webp'],
            ['alt_text' => 'Handcrafted Terracotta Aroma Diffuser Vase', 'is_primary' => true, 'sort_order' => 1]
        );

        // Product 3: Pending Approval Product (so Product Approval screen shows sample)
        Product::firstOrCreate(
            ['slug' => 'organic-ayurvedic-incense-cones'],
            [
                'vendor_id' => $vendor2->id,
                'category_id' => $catDecor->id,
                'brand_id' => $brand->id,
                'name' => 'Temple Flower Charcoal-Free Organic Dhoop Cones',
                'status' => 'pending',
                'description' => 'Recycled temple flowers handcrafted into aromatic incense cones.',
            ]
        );

        // 7. Orders & Multi-Vendor Sub-Orders
        // ORDER 1: Delivered Order with Settled/Payable funds
        $order1 = Order::firstOrCreate(
            ['order_number' => 'MTA-2026-9041'],
            [
                'user_id' => $customer1->id,
                'shipping_full_name' => 'Rohan Sharma',
                'shipping_phone' => '9811223344',
                'shipping_address_line_1' => 'Flat 402, Lotus Greens',
                'shipping_city' => 'Gurugram',
                'shipping_state' => 'Haryana',
                'shipping_country' => 'India',
                'shipping_postal_code' => '122001',
                'subtotal' => 2849.00,
                'total_amount' => 2849.00,
                'payment_status' => 'paid',
                'order_status' => 'delivered',
            ]
        );

        // Sub-Order 1 (Vendor 1) - Delivered 8 days ago (past return window -> eligible for payout batch)
        $sub1 = SubOrder::firstOrCreate(
            ['sub_order_number' => 'SUB-9041-A'],
            [
                'order_id' => $order1->id,
                'vendor_id' => $vendor1->id,
                'subtotal' => 1899.00,
                'total_amount' => 1899.00,
                'order_status' => 'delivered',
                'confirmed_at' => Carbon::now()->subDays(10),
                'shipped_at' => Carbon::now()->subDays(9),
                'delivered_at' => Carbon::now()->subDays(8),
            ]
        );

        OrderItem::firstOrCreate(
            ['sub_order_id' => $sub1->id, 'product_id' => $product1->id],
            [
                'product_variant_id' => $variant1->id,
                'product_name' => $product1->name,
                'variant_name' => $variant1->variant_name,
                'sku' => $variant1->sku,
                'quantity' => 1,
                'unit_price' => 1899.00,
                'total_amount' => 1899.00,
            ]
        );

        // 12.5% commission = 237.38 -> Net Vendor = 1661.62
        VendorSettlement::firstOrCreate(
            ['sub_order_id' => $sub1->id],
            [
                'settlement_number' => 'SETTLE-9041-A',
                'vendor_id' => $vendor1->id,
                'gross_amount' => 1899.00,
                'commission_amount' => 237.38,
                'net_amount' => 1661.62,
                'status' => 'pending', // Eligible for Payout Batch!
                'available_at' => Carbon::now()->subDay(),
            ]
        );

        // Sub-Order 2 (Vendor 2) - Delivered
        $sub2 = SubOrder::firstOrCreate(
            ['sub_order_number' => 'SUB-9041-B'],
            [
                'order_id' => $order1->id,
                'vendor_id' => $vendor2->id,
                'subtotal' => 950.00,
                'total_amount' => 950.00,
                'order_status' => 'delivered',
                'confirmed_at' => Carbon::now()->subDays(10),
                'delivered_at' => Carbon::now()->subDays(8),
            ]
        );

        OrderItem::firstOrCreate(
            ['sub_order_id' => $sub2->id, 'product_id' => $product2->id],
            [
                'product_variant_id' => $variant2->id,
                'product_name' => $product2->name,
                'variant_name' => $variant2->variant_name,
                'sku' => $variant2->sku,
                'quantity' => 1,
                'unit_price' => 950.00,
                'total_amount' => 950.00,
            ]
        );

        // 8% commission = 76.00 -> Net = 874.00
        VendorSettlement::firstOrCreate(
            ['sub_order_id' => $sub2->id],
            [
                'settlement_number' => 'SETTLE-9041-B',
                'vendor_id' => $vendor2->id,
                'gross_amount' => 950.00,
                'commission_amount' => 76.00,
                'net_amount' => 874.00,
                'status' => 'pending', // Eligible for Payout Batch!
                'available_at' => Carbon::now()->subDay(),
            ]
        );

        // ORDER 2: Active Order with Live 24h SLA Countdown
        $order2 = Order::firstOrCreate(
            ['order_number' => 'MTA-2026-9042'],
            [
                'user_id' => $customer2->id,
                'shipping_full_name' => 'Priya Verma',
                'shipping_phone' => '9877665544',
                'shipping_address_line_1' => 'House 12, Malviya Nagar',
                'shipping_city' => 'Jaipur',
                'shipping_state' => 'Rajasthan',
                'shipping_country' => 'India',
                'shipping_postal_code' => '302017',
                'subtotal' => 1899.00,
                'total_amount' => 1899.00,
                'payment_status' => 'paid',
                'order_status' => 'processing',
            ]
        );

        $subOrderActiveSla = SubOrder::firstOrCreate(
            ['sub_order_number' => 'SUB-9042-A'],
            [
                'order_id' => $order2->id,
                'vendor_id' => $vendor1->id,
                'subtotal' => 1899.00,
                'total_amount' => 1899.00,
                'order_status' => 'confirmed',
            ]
        );
        $subOrderActiveSla->created_at = Carbon::now()->subHours(6); // 18h remaining on SLA!
        $subOrderActiveSla->save();

        OrderItem::firstOrCreate(
            ['sub_order_id' => $subOrderActiveSla->id, 'product_id' => $product1->id],
            [
                'product_variant_id' => $variant1->id,
                'product_name' => $product1->name,
                'variant_name' => $variant1->variant_name,
                'sku' => $variant1->sku,
                'quantity' => 1,
                'unit_price' => 1899.00,
                'total_amount' => 1899.00,
            ]
        );

        // ORDER 3: SLA Breached Sub-Order (Over 24h unconfirmed)
        $order3 = Order::firstOrCreate(
            ['order_number' => 'MTA-2026-9043'],
            [
                'user_id' => $customer1->id,
                'shipping_full_name' => 'Rohan Sharma',
                'shipping_phone' => '9811223344',
                'shipping_address_line_1' => 'Sector 14',
                'shipping_city' => 'Gurugram',
                'shipping_state' => 'Haryana',
                'shipping_country' => 'India',
                'shipping_postal_code' => '122001',
                'subtotal' => 950.00,
                'total_amount' => 950.00,
                'payment_status' => 'paid',
                'order_status' => 'processing',
            ]
        );

        $subOrderBreached = SubOrder::firstOrCreate(
            ['sub_order_number' => 'SUB-9043-A'],
            [
                'order_id' => $order3->id,
                'vendor_id' => $vendor2->id,
                'subtotal' => 950.00,
                'total_amount' => 950.00,
                'order_status' => 'confirmed',
            ]
        );
        $subOrderBreached->created_at = Carbon::now()->subHours(26); // Breached SLA!
        $subOrderBreached->save();

        OrderItem::firstOrCreate(
            ['sub_order_id' => $subOrderBreached->id, 'product_id' => $product2->id],
            [
                'product_variant_id' => $variant2->id,
                'product_name' => $product2->name,
                'variant_name' => $variant2->variant_name,
                'sku' => $variant2->sku,
                'quantity' => 1,
                'unit_price' => 950.00,
                'total_amount' => 950.00,
            ]
        );

        // 8. Dispute / Return Case
        $returnCase = ReturnModel::firstOrCreate(
            ['return_number' => 'RET-2026-108'],
            [
                'order_id' => $order1->id,
                'user_id' => $customer1->id,
                'status' => 'pending',
                'reason' => 'Vase neck has visible hairline crack on arrival.',
                'customer_note' => 'Package arrived intact, but ceramic diffuser was cracked inside foam wrap.',
                'requested_at' => Carbon::now()->subDays(2),
            ]
        );

        ReturnItem::firstOrCreate(
            ['return_id' => $returnCase->id, 'order_item_id' => 1],
            [
                'quantity' => 1,
                'refund_amount' => 1899.00,
                'item_condition' => 'damaged',
                'status' => 'pending',
                'remarks' => 'Defective / Damaged',
            ]
        );

        // 9. Historical Completed Payout
        VendorPayout::firstOrCreate(
            ['payout_number' => 'PAY-W45-001'],
            [
                'vendor_id' => $vendor1->id,
                'vendor_settlement_id' => 1,
                'amount' => 8400.00,
                'payment_method' => 'bank_transfer',
                'transaction_reference' => 'UTR-HDFC-991208',
                'status' => 'completed',
                'processed_at' => Carbon::now()->subDays(7),
                'remarks' => 'Weekly payout batch successfully processed.',
            ]
        );
    }
}
