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
        Schema::create('sponsorship_enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no')->unique();
            $table->string('email');
            $table->foreignId('sponsor_tier_id')->nullable()->constrained('sponsor_tiers')->nullOnDelete();
            $table->string('status')->default('new'); // new, contacted, negotiation, confirmed, closed
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->text('internal_notes')->nullable();
            $table->timestamp('callback_requested_at')->nullable();
            $table->string('preferred_contact_time')->nullable();
            $table->json('field_values')->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sponsorship_enquiries');
    }
};
