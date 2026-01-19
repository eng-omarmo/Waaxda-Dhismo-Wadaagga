<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('land_parcels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('plot_number')->unique()->index(); // Official plot/cadastral number
            $table->string('title_number')->nullable()->index(); // Land title deed number
            $table->string('location_district');
            $table->string('location_region')->nullable();
            $table->decimal('size_sqm', 15, 2)->nullable();
            $table->string('current_owner_name');
            $table->string('current_owner_national_id'); // Will be encrypted in model
            $table->enum('ownership_type', ['Private', 'Shared', 'Government', 'Leased'])->default('Private');
            $table->enum('verification_status', ['Unverified', 'PendingVerification', 'Verified', 'Rejected'])->default('Unverified')->index();
            $table->unsignedBigInteger('verified_by_admin_id')->nullable()->index();
            $table->timestamp('verified_at')->nullable();
            $table->json('verification_documents_path')->nullable(); // Array of document paths
            $table->text('verification_notes')->nullable();
            $table->date('last_verification_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('land_parcels');
    }
};
