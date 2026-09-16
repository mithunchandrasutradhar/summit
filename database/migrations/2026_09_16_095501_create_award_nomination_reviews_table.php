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
        Schema::create('award_nomination_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nomination_id')->constrained('award_nominations')->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('score')->nullable();
            $table->text('comments')->nullable();
            $table->string('decision')->nullable(); // shortlist, reject, no_decision
            $table->timestamps();

            $table->unique(['nomination_id', 'reviewer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('award_nomination_reviews');
    }
};
