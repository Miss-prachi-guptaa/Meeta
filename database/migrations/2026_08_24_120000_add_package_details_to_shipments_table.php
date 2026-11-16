<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sub_orders', function (Blueprint $table) {
            $table->text('cancellation_reason')->nullable()->after('cancelled_at');
        });

        Schema::table('shipments', function (Blueprint $table) {
            $table->string('provider', 30)->default('dummy')->after('sub_order_id');
            $table->decimal('package_weight', 8, 3)->nullable()->after('status');
            $table->decimal('package_length', 8, 2)->nullable()->after('package_weight');
            $table->decimal('package_width', 8, 2)->nullable()->after('package_length');
            $table->decimal('package_height', 8, 2)->nullable()->after('package_width');
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn(['provider', 'package_weight', 'package_length', 'package_width', 'package_height']);
        });

        Schema::table('sub_orders', function (Blueprint $table) {
            $table->dropColumn('cancellation_reason');
        });
    }
};
