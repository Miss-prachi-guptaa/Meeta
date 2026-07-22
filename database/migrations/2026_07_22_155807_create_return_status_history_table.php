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
        Schema::create('return_status_history', function (Blueprint $table) {

            $table->id();

            $table->foreignId('return_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('status', 30);

            $table->text('remarks')->nullable();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('changed_at');

            $table->timestamps();

            $table->index('return_id', 'rsh_return_idx');
            $table->index('status', 'rsh_status_idx');
            $table->index('changed_at', 'rsh_changed_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_status_history');
    }
};