<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('apartment_construction_permits', function (Blueprint $table) {
            $table->text('approval_notes')->nullable();
            $table->longText('approval_signature_svg')->nullable();
            $table->unsignedBigInteger('approved_by_admin_id')->nullable()->index();
            $table->timestamp('approved_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('apartment_construction_permits', function (Blueprint $table) {
            $table->dropColumn(['approval_notes', 'approval_signature_svg', 'approved_by_admin_id', 'approved_at']);
        });
    }
};
