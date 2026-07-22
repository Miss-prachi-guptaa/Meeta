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
        Schema::create('cart_items', function (Blueprint $table) {

            $table->id();

            $table->foreignId('cart_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('product_variant_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedInteger('quantity')->default(1);

            // Price at the time of adding to cart
            $table->decimal('unit_price', 10, 2);

            $table->timestamps();

            // Same variant should not appear twice in one cart
            $table->unique(
                ['cart_id', 'product_variant_id'],
                'cart_variant_unique'
            );

            $table->index('cart_id', 'cart_item_cart_idx');
            $table->index('product_variant_id', 'cart_item_variant_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};