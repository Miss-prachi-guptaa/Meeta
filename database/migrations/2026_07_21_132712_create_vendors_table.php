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
        Schema::create('vendors', function (Blueprint $table) {

            $table->id();

            // One User = One Vendor
            $table->foreignId('user_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();

            // Store Details
            $table->string('store_name');
            $table->string('store_slug')->unique();

            $table->string('business_name');

            // Tax Details
            $table->string('gst_number')->nullable();
            $table->string('pan_number')->nullable();

            // Store Info
            $table->text('description')->nullable();

            $table->string('logo')->nullable();
            $table->string('banner')->nullable();

            // Contact
            $table->string('support_email')->nullable();
            $table->string('support_phone',20)->nullable();

            // Approval
            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'suspended'
            ])->default('pending');

            $table->timestamp('approved_at')->nullable();

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();

            // Indexes
            $table->index('status');
            $table->index('store_name');
            $table->index('business_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};