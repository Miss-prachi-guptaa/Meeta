<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('action',50);

            $table->string('module',50);

            $table->string('record_type',50);

            $table->unsignedBigInteger('record_id');

            $table->json('old_values')->nullable();

            $table->json('new_values')->nullable();

            $table->string('ip_address',45)->nullable();

            $table->string('user_agent')->nullable();

            $table->timestamp('performed_at');

            $table->timestamps();

            $table->index('user_id','audit_user_idx');
            $table->index(['record_type','record_id'],'audit_record_idx');
            $table->index('performed_at','audit_date_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};