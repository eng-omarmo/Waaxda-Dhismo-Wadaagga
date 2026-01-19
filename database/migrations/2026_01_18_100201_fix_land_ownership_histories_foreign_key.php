<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Check if table exists
        if (Schema::hasTable('land_ownership_histories')) {
            // Drop existing foreign key constraints if they exist
            try {
                DB::statement('ALTER TABLE `land_ownership_histories` DROP FOREIGN KEY IF EXISTS `land_ownership_histories_land_parcel_id_foreign`');
            } catch (\Exception $e) {
                // Ignore if constraint doesn't exist
            }
            
            // Try to drop any auto-generated constraint names
            try {
                // Get all foreign keys on this table
                $foreignKeys = DB::select("
                    SELECT CONSTRAINT_NAME 
                    FROM information_schema.KEY_COLUMN_USAGE 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'land_ownership_histories' 
                    AND COLUMN_NAME = 'land_parcel_id'
                    AND REFERENCED_TABLE_NAME IS NOT NULL
                ");
                
                foreach ($foreignKeys as $fk) {
                    try {
                        DB::statement("ALTER TABLE `land_ownership_histories` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
                    } catch (\Exception $e) {
                        // Ignore if already dropped
                    }
                }
            } catch (\Exception $e) {
                // Ignore errors
            }
            
            // Add the properly named foreign key if column exists and doesn't have constraint
            if (Schema::hasColumn('land_ownership_histories', 'land_parcel_id')) {
                try {
                    DB::statement('
                        ALTER TABLE `land_ownership_histories` 
                        ADD CONSTRAINT `land_ownership_histories_land_parcel_id_foreign` 
                        FOREIGN KEY (`land_parcel_id`) 
                        REFERENCES `land_parcels` (`id`) 
                        ON DELETE CASCADE
                    ');
                } catch (\Exception $e) {
                    // Constraint might already exist
                }
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('land_ownership_histories')) {
            try {
                DB::statement('ALTER TABLE `land_ownership_histories` DROP FOREIGN KEY IF EXISTS `land_ownership_histories_land_parcel_id_foreign`');
            } catch (\Exception $e) {
                // Ignore if doesn't exist
            }
        }
    }
};
