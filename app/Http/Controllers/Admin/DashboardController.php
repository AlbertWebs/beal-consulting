<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivity;
use App\Models\ContactSubmission;
use App\Models\ContentEntry;
use App\Models\MediaAsset;
use App\Models\WebsitePage;
use App\Models\WebsiteService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $counts = [
            'pages' => WebsitePage::query()->count(),
            'published' => WebsitePage::query()->where('status', 'published')->count(),
            'drafts' => WebsitePage::query()->where('status', 'draft')->count(),
            'unpublished' => WebsitePage::query()->where('status', 'unpublished')->count(),
            'services' => WebsiteService::query()->count(),
            'media' => MediaAsset::query()->count(),
            'submissions' => ContactSubmission::query()->count(),
            'insights' => ContentEntry::query()->where('type', 'insight')->count(),
            'projects' => ContentEntry::query()->where('type', 'project')->count(),
            'sectors' => ContentEntry::query()->where('type', 'sector')->count(),
        ];

        $contentChart = [
            ['label' => 'Pages', 'count' => $counts['pages']],
            ['label' => 'Services', 'count' => $counts['services']],
            ['label' => 'Sectors', 'count' => $counts['sectors']],
            ['label' => 'Insights', 'count' => $counts['insights']],
            ['label' => 'Experience records', 'count' => $counts['projects']],
            ['label' => 'Media assets', 'count' => $counts['media']],
            ['label' => 'Enquiries', 'count' => $counts['submissions']],
        ];
        $largestContentCount = max([1, ...array_column($contentChart, 'count')]);

        foreach ($contentChart as &$item) {
            $item['percentage'] = $item['count'] > 0 ? round($item['count'] / $largestContentCount * 100, 1) : 0;
        }

        unset($item);

        $publishingTotal = $counts['published'] + $counts['drafts'] + $counts['unpublished'];
        $publishedEndAngle = $publishingTotal > 0 ? (int) round($counts['published'] / $publishingTotal * 360) : 0;
        $draftEndAngle = $publishingTotal > 0 ? (int) round(($counts['published'] + $counts['drafts']) / $publishingTotal * 360) : 0;

        return view('admin.dashboard', [
            'counts' => $counts,
            'contentChart' => $contentChart,
            'publishingChart' => [
                'total' => $publishingTotal,
                'published' => $counts['published'],
                'drafts' => $counts['drafts'],
                'unpublished' => $counts['unpublished'],
                'publishedEndAngle' => $publishedEndAngle,
                'draftEndAngle' => $draftEndAngle,
            ],
            'health' => [
                'missing_seo' => WebsitePage::query()->whereNull('seo_description')->orWhere('seo_description', '')->count(),
                'missing_alt' => MediaAsset::query()->where('mime_type', 'like', 'image/%')->where('alt_text', '')->count(),
            ],
            'recentPages' => WebsitePage::query()->latest('updated_at')->limit(5)->get(),
            'activities' => AdminActivity::query()->with('user')->latest()->limit(8)->get(),
        ]);
    }
}
