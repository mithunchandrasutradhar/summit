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
        Schema::create('campus_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('district_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->json('institution_name');
            $table->string('slug')->unique();
            $table->string('type')->default('university'); // university, polytechnic
            $table->date('event_date')->nullable();
            $table->string('venue')->nullable();
            $table->json('description')->nullable();
            $table->string('organizer_partner')->nullable();
            $table->string('ambassador_name')->nullable();
            $table->string('ambassador_contact')->nullable();
            $table->string('status')->default('upcoming'); // upcoming, completed
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
        Schema::dropIfExists('campus_programs');
    }
};
