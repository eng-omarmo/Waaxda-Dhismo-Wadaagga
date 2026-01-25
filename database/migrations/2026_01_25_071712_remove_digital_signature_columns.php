<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('apartment_unit_transfers', function (Blueprint $table) {
            if (Schema::hasColumn('apartment_unit_transfers', 'digital_signature_svg')) {
                $table->dropColumn('digital_signature_svg');
            }
        });

        Schema::table('apartment_construction_permits', function (Blueprint $table) {
            if (Schema::hasColumn('apartment_construction_permits', 'approval_signature_svg')) {
                $table->dropColumn('approval_signature_svg');
            }
        });

        Schema::table('ownership_claims', function (Blueprint $table) {
            if (Schema::hasColumn('ownership_claims', 'reviewer_signature_svg')) {
                $table->dropColumn('reviewer_signature_svg');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('apartment_unit_transfers', function (Blueprint $table) {
            $table->text('digital_signature_svg')->nullable();
        });

        Schema::table('apartment_construction_permits', function (Blueprint $table) {
            $table->text('approval_signature_svg')->nullable();
        });

        Schema::table('ownership_claims', function (Blueprint $table) {
            $table->text('reviewer_signature_svg')->nullable();
        });
    }
};
