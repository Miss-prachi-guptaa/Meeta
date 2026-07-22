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
        Schema::create('products', function (Blueprint $table) {

            $table->id();

            // Seller
            $table->foreignId('vendor_id')
                ->constrained()
                ->cascadeOnDelete();

            // Category
            $table->foreignId('category_id')
                ->constrained()
                ->restrictOnDelete();

            // Brand
            $table->foreignId('brand_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // Product Details
            $table->string('name');

            $table->string('slug')->unique();

            $table->text('short_description')->nullable();

            $table->longText('description')->nullable();

            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            // Product Status
            $table->string('status',20)
                ->default('draft');

            // Admin Approval
            $table->timestamp('approved_at')
                ->nullable();

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->softDeletes();

            // Indexes
            $table->index('vendor_id');
            $table->index('category_id');
            $table->index('brand_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};