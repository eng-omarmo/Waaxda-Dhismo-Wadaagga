<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            try {
                $table->dropForeign(['developer_id']);
            } catch (\Throwable $e) {
                // Ignore if not present or SQLite limitation
            }
        });

        Schema::table('projects', function (Blueprint $table) {
            try {
                $table->foreign('developer_id')->references('id')->on('organizations')->nullOnDelete();
            } catch (\Throwable $e) {
                // On SQLite, foreign key alter may fail; leaving as nullable integer without constraint
            }
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            try {
                $table->dropForeign(['developer_id']);
            } catch (\Throwable $e) {
            }
        });
        Schema::table('projects', function (Blueprint $table) {
            try {
                $table->foreign('developer_id')->references('id')->on('users')->nullOnDelete();
            } catch (\Throwable $e) {
            }
        });
    }
};
