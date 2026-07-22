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
        Schema::create('customer_addresses', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // Home, Office, Hostel, etc.
            $table->string('label', 50)->default('Home');

            $table->string('full_name');

            $table->string('phone', 20);

            $table->string('address_line_1');

            $table->string('address_line_2')->nullable();

            $table->string('landmark')->nullable();

            $table->string('city');

            $table->string('state');

            $table->string('country')->default('India');

            $table->string('postal_code', 20);

            $table->decimal('latitude', 10, 7)->nullable();

            $table->decimal('longitude', 10, 7)->nullable();

            $table->boolean('is_default')->default(false);

            $table->timestamps();

            $table->softDeletes();

            $table->index('user_id', 'ca_user_idx');
            $table->index('city', 'ca_city_idx');
            $table->index('state', 'ca_state_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_addresses');
    }
};