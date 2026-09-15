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
        // Named agenda_sessions, not sessions — that table name is already
        // taken by Laravel's own database session driver.
        Schema::create('agenda_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->json('title');
            $table->string('slug')->unique();
            $table->json('description')->nullable();
            $table->string('type')->default('seminar');
            $table->string('track')->nullable();
            $table->foreignId('hall_id')->nullable()->constrained('halls')->nullOnDelete();
            $table->date('date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->boolean('is_published')->default(false);
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
        Schema::dropIfExists('agenda_sessions');
    }
};
