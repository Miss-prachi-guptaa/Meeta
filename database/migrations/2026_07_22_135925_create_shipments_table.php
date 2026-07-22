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
        Schema::create('shipments', function (Blueprint $table) {

            $table->id();

            $table->string('shipment_number', 30)->unique();

            $table->foreignId('sub_order_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('shipping_partner')->nullable();

            $table->string('tracking_number')->nullable()->unique();

            $table->string('tracking_url')->nullable();

            $table->string('status', 20)->default('pending');

            $table->timestamp('shipped_at')->nullable();

            $table->timestamp('expected_delivery_at')->nullable();

            $table->timestamp('delivered_at')->nullable();

            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->softDeletes();

            $table->index('sub_order_id', 'shipment_sub_order_idx');
            $table->index('status', 'shipment_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};