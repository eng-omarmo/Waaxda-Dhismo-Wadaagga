<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('land_ownership_histories', function (Blueprint $table) {
            $table->id();
            $table->uuid('land_parcel_id')->index();
            $table->string('owner_name');
            $table->string('owner_national_id'); // Will be encrypted in model
            $table->date('ownership_start_date')->nullable();
            $table->date('ownership_end_date')->nullable();
            $table->string('transfer_reference')->nullable();
            $table->unsignedBigInteger('recorded_by_admin_id')->nullable()->index();
            $table->timestamps();
            
            // Add foreign key constraint with explicit name
            $table->foreign('land_parcel_id', 'land_ownership_histories_land_parcel_id_foreign')
                  ->references('id')
                  ->on('land_parcels')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('land_ownership_histories');
    }
};
