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
        Schema::create('return_items', function (Blueprint $table) {

            $table->id();

            $table->foreignId('return_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('order_item_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedInteger('quantity');

            $table->decimal('refund_amount', 12, 2)->default(0);

            $table->string('item_condition', 30)->nullable();

            $table->string('status', 30)->default('requested');

            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->softDeletes();

            $table->index('return_id', 'ri_return_idx');
            $table->index('order_item_id', 'ri_order_item_idx');
            $table->index('status', 'ri_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_items');
    }
};