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
        Schema::table('page_sections', function (Blueprint $table): void {
            $table->json('draft_content')->nullable();
            $table->foreignId('draft_media_asset_id')->nullable()->constrained('media_assets')->nullOnDelete();
            $table->boolean('draft_enabled')->nullable();
        });

        Schema::table('website_services', function (Blueprint $table): void {
            $table->json('draft_content')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('website_services', function (Blueprint $table): void {
            $table->dropColumn('draft_content');
        });

        Schema::table('page_sections', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('draft_media_asset_id');
            $table->dropColumn(['draft_content', 'draft_enabled']);
        });
    }
};
