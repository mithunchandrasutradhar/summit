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
        Schema::create('exhibitor_applications', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no')->unique();
            $table->string('email');
            $table->foreignId('preferred_booth_id')->nullable()->constrained('exhibition_booths')->nullOnDelete();
            $table->string('status')->default('new'); // new, contacted, confirmed, cancelled
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->text('internal_notes')->nullable();
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
        Schema::dropIfExists('exhibitor_applications');
    }
};
