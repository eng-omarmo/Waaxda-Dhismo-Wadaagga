<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pending_registrations') && \Illuminate\Support\Facades\DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `pending_registrations` MODIFY COLUMN `status` ENUM('draft','pending','approved','rejected') NOT NULL DEFAULT 'draft'");
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('pending_registrations') && \Illuminate\Support\Facades\DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `pending_registrations` MODIFY COLUMN `status` ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending'");
        }
    }
};
