<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('online_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pending_registration_id')->constrained('pending_registrations')->cascadeOnDelete();
            $table->string('provider'); // e.g., stripe, paypal, fake
            $table->string('payment_method'); // card, paypal
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->enum('status', ['initiated', 'succeeded', 'failed'])->default('initiated');
            $table->string('transaction_id')->nullable();
            $table->string('reference')->nullable();
            $table->string('receipt_number')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('online_payments');
    }
};
