<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('engineer_licenses', function (Blueprint $table) {
            $table->id();
            $table->string('applicant_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('national_id');
            $table->string('engineering_field');
            $table->string('university')->nullable();
            $table->string('graduation_year')->nullable();
            $table->string('status')->default('Pending'); // Pending, Approved, Rejected
            $table->text('admin_comments')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('engineer_licenses');
    }
};
