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
        Schema::create('campaign_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('key'); // divisions_covered, districts_covered, institutions_activated, participants_reached
            $table->unsignedBigInteger('value')->default(0);
            $table->boolean('is_manual_override')->default(false);
            $table->timestamps();

            $table->unique(['event_id', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_stats');
    }
};
