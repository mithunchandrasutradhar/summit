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
        Schema::create('form_definitions', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // summit_registration, award_nomination, sponsorship_enquiry, exhibitor_application, forum_membership
            $table->string('name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_definitions');
    }
};
