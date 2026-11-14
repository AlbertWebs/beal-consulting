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
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 32)->default('none')->index();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
        });

        Schema::create('media_assets', function (Blueprint $table) {
            $table->id();
            $table->string('disk', 32)->default('public');
            $table->string('path');
            $table->string('thumbnail_path')->nullable();
            $table->string('original_name');
            $table->string('mime_type', 120);
            $table->unsignedBigInteger('file_size');
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->string('alt_text', 500)->default('');
            $table->boolean('is_system')->default(false);
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['disk', 'path']);
        });

        Schema::create('website_pages', function (Blueprint $table) {
            $table->id();
            $table->string('page_key', 80)->unique();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('status', 24)->default('published')->index();
            $table->string('navigation_label')->nullable();
            $table->boolean('show_in_navigation')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('seo_title')->nullable();
            $table->string('seo_description', 320)->nullable();
            $table->foreignId('open_graph_media_id')->nullable()->constrained('media_assets')->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('page_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_page_id')->constrained()->cascadeOnDelete();
            $table->string('section_key', 80);
            $table->string('label');
            $table->json('content');
            $table->foreignId('media_asset_id')->nullable()->constrained('media_assets')->nullOnDelete();
            $table->boolean('is_enabled')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['website_page_id', 'section_key']);
            $table->index(['website_page_id', 'sort_order']);
        });

        Schema::create('website_services', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('number', 12);
            $table->string('title');
            $table->string('description', 320);
            $table->longText('description_html')->nullable();
            $table->foreignId('media_asset_id')->nullable()->constrained('media_assets')->nullOnDelete();
            $table->string('image_path')->nullable();
            $table->string('image_alt', 500)->default('');
            $table->json('subservices');
            $table->json('content')->nullable();
            $table->string('status', 24)->default('published')->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('seo_title')->nullable();
            $table->string('seo_description', 320)->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['status', 'sort_order']);
        });

        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('setting_group', 80);
            $table->string('setting_key', 100)->unique();
            $table->json('value')->nullable();
            $table->foreignId('media_asset_id')->nullable()->constrained('media_assets')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['setting_group', 'setting_key']);
        });

        Schema::create('contact_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('email', 254);
            $table->string('company', 180)->nullable();
            $table->string('service', 160)->nullable();
            $table->text('message');
            $table->string('status', 24)->default('new')->index();
            $table->timestamp('read_at')->nullable();
            $table->timestamp('handled_at')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['created_at', 'status']);
        });

        Schema::create('admin_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 80);
            $table->string('subject_type', 120)->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('summary', 255);
            $table->json('properties')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            $table->index(['subject_type', 'subject_id']);
            $table->index(['user_id', 'created_at']);
        });

        Schema::create('cms_revisions', function (Blueprint $table) {
            $table->id();
            $table->string('revisable_type', 120);
            $table->unsignedBigInteger('revisable_id');
            $table->json('snapshot');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['revisable_type', 'revisable_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cms_revisions');
        Schema::dropIfExists('admin_activity_logs');
        Schema::dropIfExists('contact_submissions');
        Schema::dropIfExists('site_settings');
        Schema::dropIfExists('website_services');
        Schema::dropIfExists('page_sections');
        Schema::dropIfExists('website_pages');
        Schema::dropIfExists('media_assets');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'is_active', 'last_login_at']);
        });
    }
};
