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
        Schema::create('exhibition_booths', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('booth_no');
            $table->string('zone')->nullable();
            $table->string('size')->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->string('status')->default('available'); // available, reserved, confirmed
            $table->integer('map_x')->nullable();
            $table->integer('map_y')->nullable();
            $table->timestamps();

            $table->unique(['event_id', 'booth_no']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exhibition_booths');
    }
};
