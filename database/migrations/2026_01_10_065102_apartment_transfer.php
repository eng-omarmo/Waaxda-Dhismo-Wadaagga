<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apartment_unit_transfers', function (Blueprint $table) {
            $table->id();

            // Transfer reference
            $table->string('transfer_reference_number')->unique();

            // Apartment & unit
            $table->string('apartment_number');
            $table->string('unit_number')->nullable();
            // Previous owner
            $table->string('previous_owner_name');
            $table->string('previous_owner_id');

            // New owner
            $table->string('new_owner_name');
            $table->string('new_owner_id');

            // Transfer details
            $table->enum('transfer_reason', [
                'Sale',
                'Inheritance',
                'Gift',
            ]);

            $table->date('transfer_date');

            // Supporting documents
            $table->string('supporting_documents_path')->nullable();

            // Approval
            $table->enum('approval_status', [
                'Pending',
                'Approved',
                'Rejected',
            ])->default('Pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apartment_unit_transfers');
    }
};
