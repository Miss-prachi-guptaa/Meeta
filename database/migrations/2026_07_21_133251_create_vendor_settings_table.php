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
        Schema::create('vendor_settings', function (Blueprint $table) {

            $table->id();

            $table->foreignId('vendor_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();

            // Store Settings
            $table->boolean('accept_orders')->default(true);
            $table->boolean('auto_accept_orders')->default(false);
            $table->boolean('vacation_mode')->default(false);

            // Order Settings
            $table->unsignedSmallInteger('processing_time')->default(2); // Days

            // Notification Settings
            $table->boolean('email_notifications')->default(true);
            $table->boolean('sms_notifications')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_settings');
    }
};