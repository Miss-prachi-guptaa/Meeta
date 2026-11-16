<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->timestamp('submitted_at')->nullable()->after('status')->index();
        });

        // Existing pending applications use their most recent update as the initial queue timestamp.
        DB::table('vendors')->where('status', 'pending')->whereNull('submitted_at')
            ->update(['submitted_at' => DB::raw('updated_at')]);
    }

    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropIndex(['submitted_at']);
            $table->dropColumn('submitted_at');
        });
    }
};
