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
        Schema::create('refunds', function (Blueprint $table) {

            $table->id();

            $table->string('refund_number', 30)->unique();

            $table->foreignId('return_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('payment_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->decimal('amount', 12, 2);

            $table->string('refund_method', 30);

            $table->string('transaction_reference')->nullable();

            $table->string('status', 30)->default('pending');

            $table->text('remarks')->nullable();

            $table->timestamp('processed_at')->nullable();

            $table->timestamps();

            $table->softDeletes();

            $table->index('return_id', 'refund_return_idx');
            $table->index('payment_id', 'refund_payment_idx');
            $table->index('status', 'refund_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};