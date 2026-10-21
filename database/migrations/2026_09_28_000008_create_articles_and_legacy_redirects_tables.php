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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('category')->default('news');
            $table->string('title');
            $table->string('seo_title')->nullable();
            $table->text('lead')->nullable();
            $table->text('excerpt')->nullable();
            $table->date('published_at')->nullable();
            $table->string('reading_time')->nullable();
            $table->boolean('featured')->default(false);
            $table->string('card_image')->nullable();
            $table->string('card_alt')->nullable();
            $table->string('hero_image')->nullable();
            $table->string('hero_alt')->nullable();
            $table->string('cta_text')->nullable();
            $table->longText('body')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->index(['is_published', 'published_at']);
            $table->index(['is_published', 'category']);
        });

        Schema::create('legacy_redirects', function (Blueprint $table) {
            $table->id();
            $table->string('from_path')->unique();
            $table->string('route_name');
            $table->json('parameters')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legacy_redirects');
        Schema::dropIfExists('articles');
    }
};
