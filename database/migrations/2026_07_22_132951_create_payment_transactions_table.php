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
        Schema::create('payment_transactions', function (Blueprint $table) {

            $table->id();

            $table->foreignId('payment_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('transaction_id')->unique();

            $table->string('gateway', 30);

            $table->string('transaction_type', 20);

            $table->decimal('amount', 12, 2);

            $table->string('status', 20);

            $table->json('gateway_response')->nullable();

            $table->text('failure_reason')->nullable();

            $table->timestamp('processed_at')->nullable();

            $table->timestamps();

            $table->index('payment_id', 'pt_payment_idx');
            $table->index('transaction_id', 'pt_transaction_idx');
            $table->index('status', 'pt_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};