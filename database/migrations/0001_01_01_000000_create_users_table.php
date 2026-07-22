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
        Schema::create('users', function (Blueprint $table) {
           $table->id();

$table->string('first_name');
$table->string('last_name')->nullable();
$table->string('display_name')->nullable();

$table->string('email')->unique();
$table->string('phone',20)->unique()->nullable();

$table->timestamp('email_verified_at')->nullable();

$table->string('password');

$table->enum('status',[
    'pending_verification',
    'active',
    'suspended',
    'deleted'
])->default('pending_verification');

$table->enum('gender',[
    'male',
    'female',
    'other'
])->nullable();

$table->date('date_of_birth')->nullable();

$table->string('profile_image')->nullable();

               $table->timestamp('last_login_at')->nullable();

               $table->rememberToken();

               $table->timestamps();
               $table->softDeletes();
        });

      
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
