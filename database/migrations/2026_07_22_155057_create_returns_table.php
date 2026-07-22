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
        Schema::create('returns', function (Blueprint $table) {

            $table->id();

            $table->string('return_number', 30)->unique();

            $table->foreignId('order_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('status', 30)->default('requested');

            $table->text('reason');

            $table->text('customer_note')->nullable();

            $table->text('admin_note')->nullable();

            $table->timestamp('requested_at');

            $table->timestamp('approved_at')->nullable();

            $table->timestamp('rejected_at')->nullable();

            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            $table->softDeletes();

            $table->index('order_id', 'return_order_idx');
            $table->index('user_id', 'return_user_idx');
            $table->index('status', 'return_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('returns');
    }
};