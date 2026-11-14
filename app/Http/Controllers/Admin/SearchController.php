<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentEntry;
use App\Models\WebsitePage;
use App\Models\WebsiteService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function __invoke(Request $request): View
    {
        $query = trim((string) $request->query('q', ''));

        return view('admin.search', [
            'query' => $query,
            'pages' => $query === '' ? collect() : WebsitePage::query()->where('title', 'like', '%'.$query.'%')->limit(10)->get(),
            'services' => $query === '' || ! $request->user()->canManageCms('services') ? collect() : WebsiteService::query()->where('title', 'like', '%'.$query.'%')->limit(10)->get(),
            'content' => $query === '' || ! $request->user()->canManageCms('content') ? collect() : ContentEntry::query()->where('title', 'like', '%'.$query.'%')->limit(20)->get(),
        ]);
    }
}
