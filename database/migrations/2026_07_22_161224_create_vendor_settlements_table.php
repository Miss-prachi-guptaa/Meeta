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
        Schema::create('vendor_settlements', function (Blueprint $table) {

            $table->id();

            $table->string('settlement_number', 30)->unique();

            $table->foreignId('vendor_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('sub_order_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('payment_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->decimal('gross_amount', 12, 2);

            $table->decimal('commission_amount', 12, 2)->default(0);

            $table->decimal('tax_amount', 12, 2)->default(0);

            $table->decimal('shipping_amount', 12, 2)->default(0);

            $table->decimal('refund_amount', 12, 2)->default(0);

            $table->decimal('net_amount', 12, 2);

            $table->timestamp('available_at')->nullable();

            $table->string('status', 30)->default('pending');

            $table->timestamp('settled_at')->nullable();

            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->softDeletes();

            $table->index('vendor_id', 'vs_vendor_idx');
            $table->index('sub_order_id', 'vs_sub_order_idx');
            $table->index('payment_id', 'vs_payment_idx');
            $table->index('status', 'vs_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_settlements');
    }
};