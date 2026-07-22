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
        Schema::create('vendor_bank_accounts', function (Blueprint $table) {

            $table->id();

            $table->foreignId('vendor_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('account_holder_name');

            $table->string('bank_name');

            $table->string('branch_name')->nullable();

            $table->string('account_number');

            $table->string('ifsc_code',20);

            $table->string('upi_id')->nullable();

            $table->string('account_type',20)
                ->default('savings');

            $table->boolean('is_primary')
                ->default(true);

            $table->boolean('is_verified')
                ->default(false);

            $table->timestamp('verified_at')
                ->nullable();

            $table->foreignId('verified_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index('vendor_id');
            $table->index('is_primary');
            $table->index('is_verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_bank_accounts');
    }
};