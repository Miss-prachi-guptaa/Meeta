<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('title');

            $table->text('message');

            $table->string('type',30);

            $table->string('channel',20);

            $table->string('reference_type',50)->nullable();

            $table->unsignedBigInteger('reference_id')->nullable();

            $table->boolean('is_read')->default(false);

            $table->timestamp('read_at')->nullable();

            $table->timestamps();

            $table->softDeletes();

            $table->index('user_id','notification_user_idx');
            $table->index('is_read','notification_read_idx');
            $table->index(['reference_type','reference_id'],'notification_ref_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};