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
        Schema::create('inventory_transactions', function (Blueprint $table) {

            $table->id();

            $table->foreignId('inventory_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('product_variant_id')
                ->constrained()
                ->restrictOnDelete();

            $table->string('transaction_type', 30);

            $table->integer('quantity');

            $table->string('reference_type', 30)->nullable();

            $table->unsignedBigInteger('reference_id')->nullable();

            $table->text('remarks')->nullable();

            $table->foreignId('performed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index('inventory_id', 'it_inventory_idx');
            $table->index('product_variant_id', 'it_variant_idx');
            $table->index('transaction_type', 'it_type_idx');
            $table->index(
                ['reference_type', 'reference_id'],
                'it_reference_idx'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};