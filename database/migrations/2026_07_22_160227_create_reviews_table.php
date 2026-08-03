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
        Schema::create('reviews', function (Blueprint $table) {
$table->id();

$table->foreignId('order_item_id')
    ->constrained()
    ->cascadeOnDelete();

 $table->foreignId('product_id')
    ->constrained()
    ->cascadeOnDelete();   

$table->foreignId('user_id')
    ->constrained()
    ->cascadeOnDelete();

$table->unsignedTinyInteger('rating');

$table->string('title')->nullable();

$table->text('review_text')->nullable();

$table->boolean('is_verified_purchase')->default(true);

$table->boolean('is_approved')->default(false);

$table->timestamps();

$table->softDeletes();

            $table->unique(['order_item_id', 'user_id'], 'review_order_item_user_unique');

            $table->index('user_id', 'review_user_idx');
            $table->index('rating', 'review_rating_idx');
            $table->index('is_approved', 'review_approved_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};