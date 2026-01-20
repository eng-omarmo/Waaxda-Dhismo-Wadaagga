<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ownership_claim_changes', function (Blueprint $table) {
            $table->id();
            $table->uuid('claim_id')->index();
            $table->unsignedBigInteger('changed_by')->index();
            $table->json('changes');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ownership_claim_changes');
    }
};
