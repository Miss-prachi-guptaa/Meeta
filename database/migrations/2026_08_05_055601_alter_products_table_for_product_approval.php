<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {

            $table->text('rejection_reason')
                ->nullable()
                ->after('approved_by');
        });

        /*
        |--------------------------------------------------------------------------
        | Existing Status Values
        |--------------------------------------------------------------------------
        |
        | draft     -> Vendor is creating/editing product
        | pending   -> Submitted for admin approval
        | active    -> Approved & visible to customers
        | rejected  -> Rejected by admin
        | inactive  -> Disabled by vendor/admin
        |
        */
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {

            $table->dropColumn('rejection_reason');
        });
    }
};