<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pending_registrations', function (Blueprint $table) {
            $table->foreignId('service_id')->nullable()->after('id')->constrained('services')->nullOnDelete();
            $table->string('service_slug')->nullable()->after('service_id');
        });
    }

    public function down(): void
    {
        Schema::table('pending_registrations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('service_id');
            $table->dropColumn('service_slug');
        });
    }
};
