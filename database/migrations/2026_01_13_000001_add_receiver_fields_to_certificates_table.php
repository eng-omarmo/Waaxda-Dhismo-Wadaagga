<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->string('receiver_type')->nullable()->index();
            $table->string('receiver_id')->nullable()->index();
            $table->string('issued_to')->nullable();
            $table->string('status')->default('valid');
        });
    }

    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropColumn(['receiver_type', 'receiver_id', 'issued_to', 'status']);
        });
    }
};
