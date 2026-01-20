<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ownership_claims')) {
            Schema::table('ownership_claims', function (Blueprint $table) {
                if (! Schema::hasColumn('ownership_claims', 'apartment_id')) {
                    $table->foreignUuid('apartment_id')->nullable()->constrained('apartments')->nullOnDelete();
                }
            });
            try {
                DB::statement('ALTER TABLE ownership_claims ALTER COLUMN unit_id DROP NOT NULL');
            } catch (\Throwable $e) {
                try {
                    DB::statement('ALTER TABLE `ownership_claims` MODIFY `unit_id` CHAR(36) NULL');
                } catch (\Throwable $e2) {
                }
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('ownership_claims')) {
            Schema::table('ownership_claims', function (Blueprint $table) {
                if (Schema::hasColumn('ownership_claims', 'apartment_id')) {
                    $table->dropConstrainedForeignId('apartment_id');
                }
            });
            try {
                DB::statement('ALTER TABLE ownership_claims ALTER COLUMN unit_id SET NOT NULL');
            } catch (\Throwable $e) {
                // For MySQL, setting NOT NULL requires data compliance; skip reverting
            }
        }
    }
};
