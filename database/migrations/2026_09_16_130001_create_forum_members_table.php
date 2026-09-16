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
        // Perpetual — not scoped to an event/edition. The BACCO Forum is a
        // long-term ecosystem initiative and membership persists across
        // summit years, unlike registrations/nominations/enquiries.
        Schema::create('forum_members', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no')->unique();
            $table->string('email');
            $table->timestamp('consent_accepted_at')->nullable();
            $table->string('status')->default('submitted'); // submitted, approved, rejected
            $table->json('field_values')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_members');
    }
};
