<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up(): void
{
    Schema::create('orders', function (Blueprint $table) {

        $table->id();

        // Order Info
        $table->string('order_number', 30)->unique();

        // Customer
        $table->foreignId('user_id')
            ->constrained()
            ->cascadeOnDelete();

        // Shipping Address Snapshot
        $table->string('shipping_full_name');

        $table->string('shipping_phone', 20);

        $table->string('shipping_address_line_1');

        $table->string('shipping_address_line_2')->nullable();

        $table->string('shipping_landmark')->nullable();

        $table->string('shipping_city');

        $table->string('shipping_state');

        $table->string('shipping_country')->default('India');

        $table->string('shipping_postal_code', 20);

        // Order Amounts
        $table->decimal('subtotal', 12, 2)->default(0);

        $table->decimal('discount_amount', 12, 2)->default(0);

        $table->decimal('tax_amount', 12, 2)->default(0);

        $table->decimal('shipping_amount', 12, 2)->default(0);

        $table->decimal('total_amount', 12, 2);
       
          
        // Status
        $table->enum('payment_status', [
            'pending',
            'paid',
            'failed',
            'refunded',
            'partially_refunded'
        ])->default('pending');

        $table->enum('order_status', [
            'pending',
            'confirmed',
            'processing',
            'shipped',
            'delivered',
            'cancelled',
            'returned'
        ])->default('pending');

        $table->text('notes')->nullable();

        $table->timestamps();

        $table->softDeletes();

        // Indexes
        $table->index('user_id', 'order_user_idx');
        $table->index('payment_status', 'order_payment_status_idx');
        $table->index('order_status', 'order_status_idx');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};