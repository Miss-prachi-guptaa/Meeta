<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otp_verifications', function (Blueprint $table) {

            $table->id();

            // User optional hai kyunki signup ke time account exist nahi bhi kar sakta
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();

            // Email ya Phone
            $table->string('identifier');

            // email / phone
            $table->string('type',20);

            // registration / login / password_reset / email_verification / phone_verification
            $table->string('purpose',30);

            // OTP ko plain text me store nahi karenge
            $table->string('otp_hash');

            $table->unsignedTinyInteger('attempts')->default(0);

            $table->timestamp('expires_at');

            $table->timestamp('verified_at')->nullable();

            $table->timestamps();

            $table->index(['identifier','purpose']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otp_verifications');
    }
};