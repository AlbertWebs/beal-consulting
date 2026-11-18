<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $project = DB::table('content_entries')
            ->where('type', 'project')
            ->where('slug', 'africa-area-office-development')
            ->first(['id']);

        if ($project === null) {
            return;
        }

        $galleryAssets = [
            ['filename' => 'africa-area-office-blueprints.webp', 'stage' => 'blueprints', 'alt_text' => 'AI illustration of the proposed office in architectural drawings; not an actual project drawing.'],
            ['filename' => 'africa-area-office-blueprints-detail.png', 'stage' => 'blueprints', 'alt_text' => 'AI-generated illustrative plan set with office elevations and site layout; not an actual project drawing.'],
            ['filename' => 'africa-area-office-visualisation.webp', 'stage' => 'visualisations', 'alt_text' => 'AI concept visualisation of a modern regional office in Nairobi; not a rendering from the actual project.'],
            ['filename' => 'africa-area-office-visualisation-detail.png', 'stage' => 'visualisations', 'alt_text' => 'AI-generated exterior concept of a regional office in Nairobi; not an approved or actual project rendering.'],
            ['filename' => 'africa-area-office-construction.webp', 'stage' => 'in_progress', 'alt_text' => 'AI-generated illustrative work-in-progress scene; not a photograph of this project.'],
            ['filename' => 'africa-area-office-construction-detail.png', 'stage' => 'in_progress', 'alt_text' => 'AI-generated illustrative construction progress scene; not a photograph of this project.'],
            ['filename' => 'africa-area-office-completed.webp', 'stage' => 'completed', 'alt_text' => 'AI concept view of a finished regional office; not a photograph of the actual completed project.'],
            ['filename' => 'africa-area-office-completed-detail.png', 'stage' => 'completed', 'alt_text' => 'AI-generated concept view of a completed regional office; not a photograph of the actual project.'],
        ];
        $assetDisk = Storage::disk('assets');
        $galleryMediaIds = [];
        $galleryStages = [];

        foreach ($galleryAssets as $galleryAsset) {
            $path = 'project-gallery/'.$galleryAsset['filename'];

            if (! $assetDisk->exists($path)) {
                throw new RuntimeException('Missing sample project gallery asset: '.$path);
            }

            $existingAsset = DB::table('media_assets')
                ->where('disk', 'assets')
                ->where('path', $path)
                ->first(['id']);

            if ($existingAsset !== null) {
                $assetId = (int) $existingAsset->id;
                DB::table('media_assets')->where('id', $assetId)->update([
                    'alt_text' => $galleryAsset['alt_text'],
                    'updated_at' => now(),
                ]);
            } else {
                $dimensions = @getimagesize($assetDisk->path($path)) ?: [null, null, 'mime' => 'application/octet-stream'];
                $assetId = (int) DB::table('media_assets')->insertGetId([
                    'disk' => 'assets',
                    'path' => $path,
                    'original_name' => $galleryAsset['filename'],
                    'mime_type' => $dimensions['mime'] ?? 'application/octet-stream',
                    'file_size' => $assetDisk->size($path),
                    'width' => $dimensions[0],
                    'height' => $dimensions[1],
                    'alt_text' => $galleryAsset['alt_text'],
                    'is_system' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $galleryMediaIds[] = $assetId;
            $galleryStages[$assetId] = $galleryAsset['stage'];
        }

        DB::table('content_entries')->where('id', $project->id)->update([
            'gallery_media_ids' => json_encode($galleryMediaIds, JSON_THROW_ON_ERROR),
            'gallery_stages' => json_encode($galleryStages, JSON_THROW_ON_ERROR),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Preserve the published sample gallery when rolling back code deployments.
    }
};
