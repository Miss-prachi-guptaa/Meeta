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
        Schema::create('wishlist_items', function (Blueprint $table) {

            $table->id();

            $table->foreignId('wishlist_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('product_variant_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamps();

            // Prevent duplicate products in the same wishlist
            $table->unique(
                ['wishlist_id', 'product_variant_id'],
                'wishlist_variant_unique'
            );

            $table->index('wishlist_id', 'wishlist_item_idx');
            $table->index('product_variant_id', 'wishlist_variant_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wishlist_items');
    }
};