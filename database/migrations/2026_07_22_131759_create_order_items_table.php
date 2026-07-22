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
        Schema::create('order_items', function (Blueprint $table) {

            $table->id();

            $table->foreignId('sub_order_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('product_variant_id')
                ->constrained()
                ->restrictOnDelete();

            // Snapshot
            $table->string('product_name');

            $table->string('variant_name')->nullable();

            $table->string('sku');

            $table->unsignedInteger('quantity');

            $table->decimal('unit_price', 12, 2);

            $table->decimal('discount_amount', 12, 2)->default(0);

            $table->decimal('tax_amount', 12, 2)->default(0);

            $table->decimal('total_amount', 12, 2);

            $table->timestamps();

            $table->index('sub_order_id', 'oi_sub_order_idx');
            $table->index('product_id', 'oi_product_idx');
            $table->index('product_variant_id', 'oi_variant_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};