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
        Schema::create('award_nominations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('award_categories')->cascadeOnDelete();
            $table->string('reference_no')->unique();
            $table->string('nominee_email');
            $table->timestamp('declaration_accepted_at')->nullable();
            $table->string('status')->default('submitted'); // submitted, under_review, shortlisted, winner, rejected, incomplete
            $table->boolean('is_public_shortlisted')->default(false);
            $table->boolean('is_public_winner')->default(false);
            $table->timestamp('decided_at')->nullable();
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
        Schema::dropIfExists('award_nominations');
    }
};
