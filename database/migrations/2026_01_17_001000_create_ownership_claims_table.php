<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ownership_claims', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('unit_id')->index();
            $table->string('claimant_name');
            $table->string('claimant_national_id');
            $table->string('claimant_phone')->nullable();
            $table->string('claimant_email')->nullable();
            $table->json('evidence_documents')->nullable();
            $table->enum('status', ['Pending', 'Verified', 'Rejected'])->default('Pending');
            $table->text('reviewer_comments')->nullable();
            $table->unsignedBigInteger('reviewed_by_admin_id')->nullable()->index();
            $table->timestamp('reviewed_at')->nullable();
            $table->longText('reviewer_signature_svg')->nullable();
            $table->unsignedBigInteger('last_modified_by_admin_id')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ownership_claims');
    }
};
