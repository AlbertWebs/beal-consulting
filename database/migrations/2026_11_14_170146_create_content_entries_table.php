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
        Schema::create('content_entries', function (Blueprint $table): void {
            $table->id();
            $table->string('type', 24)->index();
            $table->string('title', 180);
            $table->string('slug', 180);
            $table->string('summary', 600)->nullable();
            $table->longText('content_html')->nullable();
            $table->string('category', 120)->nullable();
            $table->string('author', 160)->nullable();
            $table->string('location', 180)->nullable();
            $table->string('scale', 180)->nullable();
            $table->text('scope')->nullable();
            $table->text('outcomes')->nullable();
            $table->string('experience_source', 32)->nullable();
            $table->foreignId('media_asset_id')->nullable()->constrained('media_assets')->nullOnDelete();
            $table->string('image_alt', 500)->default('');
            $table->string('status', 24)->default('draft')->index();
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->string('seo_title')->nullable();
            $table->string('seo_description', 320)->nullable();
            $table->json('draft_data')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['type', 'slug']);
            $table->index(['type', 'status', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_entries');
    }
};
