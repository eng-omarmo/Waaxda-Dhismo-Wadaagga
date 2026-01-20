<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('land_ownership_verifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('land_parcel_id')->index();
            $table->enum('verification_request_type', ['PrePermit', 'PreConstruction', 'Transfer'])->default('PrePermit');
            $table->unsignedBigInteger('requested_by_admin_id')->nullable()->index();
            $table->string('applicant_national_id'); // Will be encrypted in model
            $table->string('applicant_name');
            $table->enum('status', ['Pending', 'InProgress', 'Verified', 'Rejected'])->default('Pending')->index();
            $table->enum('verification_method', ['Database', 'Manual', 'ExternalAPI'])->default('Manual');
            $table->json('verification_result')->nullable(); // Structured result data
            $table->unsignedBigInteger('verified_by_admin_id')->nullable()->index();
            $table->timestamp('verified_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            // Add foreign key constraint with explicit name
            $table->foreign('land_parcel_id', 'land_ownership_verifications_land_parcel_id_foreign')
                ->references('id')
                ->on('land_parcels')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('land_ownership_verifications');
    }
};
