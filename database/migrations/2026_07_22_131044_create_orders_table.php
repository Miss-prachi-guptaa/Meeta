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

            $table->string('order_number', 30)->unique();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('customer_address_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->decimal('subtotal', 12, 2)->default(0);

            $table->decimal('discount_amount', 12, 2)->default(0);

            $table->decimal('tax_amount', 12, 2)->default(0);

            $table->decimal('shipping_amount', 12, 2)->default(0);

            $table->decimal('total_amount', 12, 2);

            $table->string('payment_status', 20)
                ->default('pending');

            $table->string('order_status', 20)
                ->default('pending');

            $table->text('notes')->nullable();

            $table->timestamp('placed_at')->nullable();

            $table->timestamps();

            $table->softDeletes();

            $table->index('user_id', 'order_user_idx');
            $table->index('order_status', 'order_status_idx');
            $table->index('payment_status', 'payment_status_idx');
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