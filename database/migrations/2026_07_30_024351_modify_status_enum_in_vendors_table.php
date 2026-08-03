<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
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