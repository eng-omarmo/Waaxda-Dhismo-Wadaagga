<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apartment_construction_permits', function (Blueprint $table) {
            $table->id();

            // Applicant
            $table->string('applicant_name');
            $table->string('national_id_or_company_registration');

            // Land information
            $table->string('land_plot_number');
            $table->string('location');

            // Apartment details
            $table->unsignedInteger('number_of_floors');
            $table->unsignedInteger('number_of_units');

            // Technical approval
            $table->string('approved_drawings_path')->nullable();
            $table->string('engineer_or_architect_name');
            $table->string('engineer_or_architect_license')->nullable();

            // Permit status
            $table->date('permit_issue_date')->nullable();
            $table->date('permit_expiry_date')->nullable();
            $table->enum('permit_status', [
                'Pending',
                'Approved',
                'Rejected'
            ])->default('Pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apartment_construction_permits');
    }
};
