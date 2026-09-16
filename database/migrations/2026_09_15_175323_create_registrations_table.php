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
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('reference_no')->unique();
            $table->string('email');
            $table->string('mobile')->nullable();
            $table->foreignId('attendee_type_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('consent_accepted_at')->nullable();
            $table->string('qr_token')->unique()->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->string('status')->default('registered'); // registered, cancelled
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
        Schema::dropIfExists('registrations');
    }
};
