<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_licenses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('company_name');
            $table->enum('license_type', ['Rental', 'Commercial']);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->enum('verification_status', ['unverified', 'verified'])->default('unverified');
            $table->date('expires_at')->nullable();
            $table->text('admin_comments')->nullable();
            $table->string('registrant_name')->nullable();
            $table->string('registrant_email')->nullable();
            $table->string('registrant_phone')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_licenses');
    }
};
