<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (\Illuminate\Support\Facades\DB::connection()->getDriverName() !== 'mysql') {
            return;
        }
        Schema::table('certificates', function (Blueprint $table) {
            if (Schema::hasColumn('certificates', 'project_id')) {
                $table->dropColumn('project_id');
            }
        });
    }

    public function down(): void
    {
        if (\Illuminate\Support\Facades\DB::connection()->getDriverName() !== 'mysql') {
            return;
        }
        Schema::table('certificates', function (Blueprint $table) {
            if (! Schema::hasColumn('certificates', 'project_id')) {
                $table->string('project_id')->nullable()->index();
            }
        });
    }
};
