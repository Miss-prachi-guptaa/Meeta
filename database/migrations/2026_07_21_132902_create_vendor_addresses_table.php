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
        Schema::create('vendor_addresses', function (Blueprint $table) {

            $table->id();

            $table->foreignId('vendor_id')
                ->constrained()
                ->cascadeOnDelete();

            // business | pickup | return
            $table->enum('type', [
                'business',
                'pickup',
                'return'
            ]);

            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();

            $table->string('landmark')->nullable();

            $table->string('city');
            $table->string('state');
            $table->string('country');
            $table->string('postal_code', 20);

            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->boolean('is_default')->default(false);

            $table->timestamps();

            $table->index('type');
            $table->index(['vendor_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_addresses');
    }
};