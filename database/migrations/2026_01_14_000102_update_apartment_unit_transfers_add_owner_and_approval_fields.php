<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('apartment_unit_transfers', function (Blueprint $table) {
            $table->foreignId('owner_profile_previous_id')->nullable()->constrained('owner_profiles');
            $table->foreignId('owner_profile_new_id')->nullable()->constrained('owner_profiles');
            $table->unsignedBigInteger('approved_by_admin_id')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_reason')->nullable();
            $table->longText('digital_signature_svg')->nullable();
            $table->timestamp('notarized_at')->nullable();
            $table->string('tax_clearance_code')->nullable();
            $table->enum('lien_check_status', ['clear', 'lien_found', 'pending'])->default('pending');
            $table->string('deed_pdf_path')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('apartment_unit_transfers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('owner_profile_previous_id');
            $table->dropConstrainedForeignId('owner_profile_new_id');
            $table->dropColumn([
                'approved_by_admin_id',
                'approved_at',
                'approval_reason',
                'digital_signature_svg',
                'notarized_at',
                'tax_clearance_code',
                'lien_check_status',
                'deed_pdf_path',
            ]);
        });
    }
};
