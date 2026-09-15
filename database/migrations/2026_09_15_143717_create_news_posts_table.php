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
        Schema::create('news_posts', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->string('slug')->unique();
            $table->string('category')->default('news'); // news, press_release
            $table->json('excerpt')->nullable();
            $table->json('body');
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->seoMeta();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news_posts');
    }
};
