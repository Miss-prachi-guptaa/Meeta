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
        Schema::create('vendor_payouts', function (Blueprint $table) {

            $table->id();

            $table->string('payout_number', 30)->unique();

            $table->foreignId('vendor_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('vendor_settlement_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->decimal('amount', 12, 2);

            $table->string('payment_method', 30);

            $table->string('transaction_reference')->nullable();

            $table->string('status', 30)->default('pending');

            $table->timestamp('processed_at')->nullable();

            $table->foreignId('processed_by')
    ->nullable()
    ->constrained('users')
    ->nullOnDelete();

$table->json('gateway_response')->nullable();

            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->softDeletes();

            $table->index('vendor_id', 'vp_vendor_idx');
            $table->index('vendor_settlement_id', 'vp_settlement_idx');
            $table->index('status', 'vp_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_payouts');
    }
};