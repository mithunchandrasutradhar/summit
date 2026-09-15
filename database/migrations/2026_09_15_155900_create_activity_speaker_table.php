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
        Schema::create('activity_speaker', function (Blueprint $table) {
            $table->id();
            $table->morphs('activity'); // activity_type, activity_id — District or CampusProgram
            $table->foreignId('speaker_id')->constrained()->cascadeOnDelete();
            $table->string('role')->default('speaker'); // speaker, mentor
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_speaker');
    }
};
