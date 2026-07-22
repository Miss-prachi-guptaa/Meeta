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
        Schema::create('payments', function (Blueprint $table) {

            $table->id();

            $table->string('payment_number', 30)->unique();

            $table->foreignId('order_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->restrictOnDelete();

            $table->decimal('amount', 12, 2);

            $table->string('currency', 10)->default('INR');

            $table->string('payment_method', 30);

            $table->string('status', 20)->default('pending');

            $table->timestamp('paid_at')->nullable();

            $table->timestamps();

            $table->softDeletes();

            $table->index('order_id', 'pay_order_idx');
            $table->index('user_id', 'pay_user_idx');
            $table->index('status', 'pay_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};