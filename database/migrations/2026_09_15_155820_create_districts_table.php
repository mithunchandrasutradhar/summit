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
        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('division_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->json('name');
            $table->string('slug')->unique();
            $table->json('description')->nullable();
            $table->string('venue')->nullable();
            $table->date('event_date')->nullable();
            $table->string('organizer_partner')->nullable();
            $table->string('status')->default('upcoming'); // upcoming, completed
            $table->unsignedInteger('participants_count')->default(0);
            $table->unsignedInteger('order')->default(0);
            $table->seoMeta();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('districts');
    }
};
