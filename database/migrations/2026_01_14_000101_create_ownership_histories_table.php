<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ownership_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('apartment_id')->constrained('apartments')->onDelete('cascade');
            $table->foreignId('owner_profile_id')->constrained('owner_profiles')->onDelete('cascade');
            $table->date('started_at')->nullable();
            $table->date('ended_at')->nullable();
            $table->string('transfer_reference_number')->nullable();
            $table->unsignedBigInteger('recorded_by_admin_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ownership_histories');
    }
};
