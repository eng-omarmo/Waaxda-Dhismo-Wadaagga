<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organization_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')
                ->constrained()
                ->onDelete('cascade');

            $table->string('file_name');      // Original name
            $table->string('file_path');      // Path in storage/app/public
            $table->string('file_type');      // e.g., pdf, png
            $table->string('document_label')  // e.g., "Business License"
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organization_documents');
    }
};
