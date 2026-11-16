<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("
            ALTER TABLE vendors
            MODIFY status ENUM(
                'draft',
                'pending',
                'approved',
                'rejected',
                'suspended'
            ) NOT NULL DEFAULT 'draft'
        ");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::table('vendors')->where('status', 'draft')->update(['status' => 'pending']);

        DB::statement("
            ALTER TABLE vendors
            MODIFY status ENUM(
                'pending',
                'approved',
                'rejected',
                'suspended'
            ) NOT NULL DEFAULT 'pending'
        ");
    }
};
