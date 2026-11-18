<?php

namespace Database\Seeders;

use App\Models\ContentEntry;
use App\Models\MediaAsset;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FounderProjectDraftsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = [
            'Itare Fresh Water Dam Project',
            'Onyx Apartments',
            'East African Breweries Limited (EABL) Headquarters Fit-out',
            'Scania East Africa Facilities',
            'EABL Microbrewery Project',
            'Africa Area Office Development',
            'British American Tobacco (BAT) Headquarters Refurbishment',
        ];
        $projectGalleryAssets = collect(config('cms.project_gallery_assets'));
        $galleryMediaByPath = MediaAsset::query()
            ->where('disk', 'assets')
            ->whereIn('path', $projectGalleryAssets->pluck('path'))
            ->get()
            ->keyBy('path');

        foreach ($projects as $index => $title) {
            $slug = Str::slug($title);
            $isAfricaAreaOfficeDevelopment = $title === 'Africa Area Office Development';
            $galleryMediaIds = [];
            $galleryStages = [];

            if ($isAfricaAreaOfficeDevelopment) {
                foreach ($projectGalleryAssets as $galleryAssetDefinition) {
                    $galleryAsset = $galleryMediaByPath->get($galleryAssetDefinition['path']);

                    if ($galleryAsset) {
                        $galleryMediaIds[] = $galleryAsset->id;
                        $galleryStages[$galleryAsset->id] = $galleryAssetDefinition['stage'];
                    }
                }
            }

            $summary = $isAfricaAreaOfficeDevelopment
                ? 'Our team’s earlier experience on Africa Area Office Development informs how we connect project governance, coordination and delivery performance at BEAL.'
                : 'Project experience gained by the Founding Director through a previous professional engagement.';
            $contentHtml = $isAfricaAreaOfficeDevelopment
                ? <<<'HTML'
                    <p>An area office must do more than provide space for people to work. It needs to support day-to-day operations, serve the people who use it, and remain practical to maintain. Shaping a development around those needs starts with a clear brief and decisions that stay connected as the work moves from planning into delivery.</p>
                    <p>For an office development of this kind, design, approvals, procurement, building services, programme and cost are closely linked. A change to the brief can affect design coordination; a gap in procurement can disrupt the sequence on site; and an unresolved interface can create rework or make handover harder. Teams need timely information, clear responsibilities and a shared view of progress to address these issues while there is still room to act.</p>
                    <p>Our team’s earlier professional experience on Africa Area Office Development informs how we approach complex projects at BEAL today. We look across governance and decision-making, design and delivery interfaces, schedule and cost performance, emerging risks, quality, safety and handover readiness. Bringing these areas into one clear picture helps clients understand what is happening, where attention is needed and who should take the next step.</p>
                    <p>This experience was gained through our team’s earlier professional roles, before BEAL Consulting Ltd. The project was not contracted by BEAL. We are keeping this account focused on the experience that informs our work today while verified details about the project and our team’s specific responsibilities are confirmed for publication.</p>
                    HTML
                : '<p>This project is included as part of the Founding Director’s professional experience. It is not presented as an engagement contracted by BEAL Consulting Ltd.</p>';
            $draftData = [
                'title' => $title,
                'slug' => $slug,
                'summary' => $summary,
                'content_html' => $contentHtml,
                'category' => '',
                'author' => '',
                'location' => '',
                'scale' => '',
                'scope' => '',
                'outcomes' => '',
                'experience_source' => 'previous_professional',
                'media_asset_id' => null,
                'gallery_media_ids' => $galleryMediaIds,
                'gallery_stages' => $galleryStages,
                'image_alt' => '',
                'seo_title' => '',
                'seo_description' => '',
                'sort_order' => ($index + 1) * 10,
                'is_featured' => false,
                'published_at' => null,
            ];

            $entry = ContentEntry::query()->firstOrCreate(
                ['type' => 'project', 'slug' => $slug],
                [
                    ...$draftData,
                    'status' => 'published',
                    'published_at' => now(),
                    'draft_data' => null,
                ],
            );

            $untouchedDraftData = [...$draftData, 'summary' => '', 'content_html' => ''];
            if ($entry->wasRecentlyCreated || $entry->status !== 'draft' || $entry->draft_data != $untouchedDraftData) {
                continue;
            }

            $entry->update([
                ...$draftData,
                'status' => 'published',
                'published_at' => now(),
                'draft_data' => null,
            ]);
        }
    }
}
