<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();

            // Organization Info
            $table->string('name');
            $table->string('registration_number')->unique()->nullable();
            $table->string('address');
            $table->enum('type', ['Developer', 'Contractor', 'Consultant', 'Other']);

            // Owner / Contact Person Info
            $table->string('contact_full_name');
            $table->string('contact_role');
            $table->string('contact_phone');
            $table->string('contact_email');

            // Defaulting to 'pending' for administrative review
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable(); // For rejection reasons

            $table->timestamps();
            $table->softDeletes(); // Recommended for government records
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
