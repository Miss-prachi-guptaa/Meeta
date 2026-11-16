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
        Schema::create('product_variants', function (Blueprint $table) {

            $table->id();

            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnDelete();

            // SKU & Barcode
            $table->string('sku')->unique();
            $table->string('barcode')->nullable()->unique();

            // Variant Name
            $table->string('name')->nullable();

            // Pricing
            $table->decimal('price', 10, 2);

            $table->decimal('sale_price', 10, 2)
                ->nullable();

            // Physical Details
            $table->decimal('weight', 8, 2)
                ->nullable();

            $table->decimal('length', 8, 2)
                ->nullable();

            $table->decimal('width', 8, 2)
                ->nullable();

            $table->decimal('height', 8, 2)
                ->nullable();

            // Status
            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();

            $table->softDeletes();

            // Indexes
            $table->index('product_id');
            $table->index('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
