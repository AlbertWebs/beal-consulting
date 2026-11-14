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
        return view('admin.dashboard', [
            'counts' => [
                'pages' => WebsitePage::query()->count(),
                'published' => WebsitePage::query()->where('status', 'published')->count(),
                'drafts' => WebsitePage::query()->where('status', 'draft')->count(),
                'services' => WebsiteService::query()->count(),
                'media' => MediaAsset::query()->count(),
                'submissions' => ContactSubmission::query()->count(),
                'insights' => ContentEntry::query()->where('type', 'insight')->count(),
                'projects' => ContentEntry::query()->where('type', 'project')->count(),
                'sectors' => ContentEntry::query()->where('type', 'sector')->count(),
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
