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
    Schema::create('services', function (Blueprint $table) {
        $table->id();
        $table->string('name', 100);
        $table->string('slug')->unique(); // For clean URLs
        $table->text('description', 500);
        $table->decimal('price', 10, 2);
        $table->string('icon_color')->nullable(); // Stores 'bg-success', etc.
        $table->string('icon_class')->nullable(); // Stores 'bi-tools', etc.
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
