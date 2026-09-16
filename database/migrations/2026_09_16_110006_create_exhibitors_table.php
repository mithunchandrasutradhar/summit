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
        Schema::create('exhibitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('exhibitor_applications')->cascadeOnDelete();
            $table->string('company_name');
            $table->string('website')->nullable();
            $table->text('description')->nullable();
            $table->string('sector')->nullable();
            $table->foreignId('booth_id')->nullable()->constrained('exhibition_booths')->nullOnDelete();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exhibitors');
    }
};
