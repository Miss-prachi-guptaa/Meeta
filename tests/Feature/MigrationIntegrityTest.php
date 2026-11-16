<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentTransaction;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MigrationIntegrityTest extends TestCase
{
    use RefreshDatabase;

    public function test_fresh_schema_contains_customer_and_commerce_dependencies(): void
    {
        $expectedColumns = [
            'vendors' => ['user_id', 'status', 'submitted_at', 'rejection_reason'],
            'product_variants' => ['product_id', 'price'],
            'order_items' => ['sub_order_id', 'product_id', 'product_variant_id'],
            'sub_orders' => ['order_id', 'vendor_id', 'order_status', 'cancellation_reason'],
            'shipments' => ['sub_order_id', 'provider', 'tracking_number', 'package_weight', 'package_length', 'package_width', 'package_height'],
            'payment_transactions' => ['payment_id', 'transaction_id'],
            'customer_preferences' => ['user_id', 'order_updates'],
            'orders' => ['user_id', 'order_status', 'cancellation_reason'],
        ];

        foreach ($expectedColumns as $table => $columns) {
            $this->assertTrue(Schema::hasTable($table), "Missing table: {$table}");
            foreach ($columns as $column) {
                $this->assertTrue(Schema::hasColumn($table, $column), "Missing column: {$table}.{$column}");
            }
        }

        $this->assertFalse(Schema::hasColumn('product_variants', 'stock'));
    }

    public function test_commerce_models_resolve_to_the_correct_relationships(): void
    {
        $this->assertInstanceOf(BelongsTo::class, (new PaymentTransaction)->payment());
        $this->assertInstanceOf(HasMany::class, (new Payment)->transactions());
        $this->assertInstanceOf(HasManyThrough::class, (new Order)->orderItems());
        $this->assertInstanceOf(HasManyThrough::class, (new Order)->shipments());
    }
}
