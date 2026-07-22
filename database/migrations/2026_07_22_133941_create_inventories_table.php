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
        Schema::create('inventories', function (Blueprint $table) {

            $table->id();

            $table->foreignId('product_variant_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedInteger('quantity')->default(0);

            $table->unsignedInteger('reserved_quantity')->default(0);

            $table->unsignedInteger('minimum_stock')->default(0);

            $table->unsignedInteger('maximum_stock')->nullable();

            $table->timestamps();

            // One inventory record per variant
            $table->unique('product_variant_id', 'inventory_variant_unique');

            $table->index('quantity', 'inventory_qty_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};