<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_license_changes', function (Blueprint $table) {
            $table->id();
            $table->string('license_id');
            $table->unsignedBigInteger('changed_by')->index();
            $table->json('changes');
            $table->timestamps();
            $table->index('license_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_license_changes');
    }
};
