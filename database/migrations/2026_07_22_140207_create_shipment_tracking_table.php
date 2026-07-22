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
        Schema::create('shipment_tracking', function (Blueprint $table) {

            $table->id();

            $table->foreignId('shipment_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('status', 30);

            $table->string('location')->nullable();

            $table->text('description')->nullable();

            // Extra data from courier API
            $table->json('meta')->nullable();

            $table->timestamp('tracked_at');

            $table->timestamps();

            $table->index('shipment_id', 'st_ship_idx');
            $table->index('status', 'st_status_idx');
            $table->index('tracked_at', 'st_tracked_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_tracking');
    }
};