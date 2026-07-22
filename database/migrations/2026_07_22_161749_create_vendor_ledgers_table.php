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
        Schema::create('vendor_ledgers', function (Blueprint $table) {

            $table->id();

            $table->foreignId('vendor_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('reference_type', 30);

            $table->unsignedBigInteger('reference_id');

            $table->string('transaction_type', 20);

            $table->decimal('amount', 12, 2);

            $table->decimal('balance_after', 12, 2);

            $table->string('status', 20)->default('completed');

            $table->text('remarks')->nullable();

            $table->timestamp('transaction_date');

            $table->timestamps();

            $table->softDeletes();

            $table->index('vendor_id', 'vl_vendor_idx');
            $table->index(['reference_type', 'reference_id'], 'vl_reference_idx');
            $table->index('transaction_date', 'vl_date_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_ledgers');
    }
};