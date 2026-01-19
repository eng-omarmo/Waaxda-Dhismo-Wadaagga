<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_licenses', function (Blueprint $table) {
            if (! Schema::hasColumn('business_licenses', 'project_id')) {
                $table->foreignUuid('project_id')->nullable()->constrained('projects')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('business_licenses', function (Blueprint $table) {
            if (Schema::hasColumn('business_licenses', 'project_id')) {
                $table->dropConstrainedForeignId('project_id');
            }
        });
    }
};
