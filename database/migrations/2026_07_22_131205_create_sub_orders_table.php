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
        Schema::create('sub_orders', function (Blueprint $table) {

            $table->id();

            $table->string('sub_order_number', 30)->unique();

            $table->foreignId('order_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('vendor_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->decimal('subtotal', 12, 2)->default(0);

            $table->decimal('discount_amount', 12, 2)->default(0);

            $table->decimal('tax_amount', 12, 2)->default(0);

            $table->decimal('shipping_amount', 12, 2)->default(0);

            $table->decimal('total_amount', 12, 2);

            $table->string('order_status', 20)
                ->default('pending');

            $table->timestamp('confirmed_at')->nullable();

            $table->timestamp('shipped_at')->nullable();

            $table->timestamp('delivered_at')->nullable();

            $table->timestamp('cancelled_at')->nullable();

            $table->timestamps();

            $table->softDeletes();

            $table->index('order_id', 'sub_order_order_idx');
            $table->index('vendor_id', 'sub_order_vendor_idx');
            $table->index('order_status', 'sub_order_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_orders');
    }
};