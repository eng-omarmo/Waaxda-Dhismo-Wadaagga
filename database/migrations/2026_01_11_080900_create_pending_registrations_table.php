<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pending_registrations')) {
            Schema::create('pending_registrations', function (Blueprint $table) {
                $table->id();
                $table->string('full_name');
                $table->string('email');
                $table->string('phone')->nullable();
                $table->enum('status', ['draft', 'pending', 'approved', 'rejected'])->default('draft');
                $table->unsignedTinyInteger('step')->default(1);
                $table->string('resume_token')->nullable()->index();
                $table->json('data')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pending_registrations');
    }
};
