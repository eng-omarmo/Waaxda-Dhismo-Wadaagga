<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('project_name');
            $table->string('location_text');
            $table->foreignId('developer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 20)->default('Draft');
            $table->string('registrant_name');
            $table->string('registrant_phone');
            $table->string('registrant_email');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
