<?php

use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\AuthController as CmsAuthController;
use App\Http\Controllers\Admin\ContactSubmissionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MediaAssetController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\SearchController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WebsiteServiceController;
use App\Http\Controllers\StoreEnquiryController;
use App\Models\PageSection;
use App\Models\WebsitePage;
use App\Models\WebsiteService;
use App\Services\WebsiteSettings;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $isPreview = request()->boolean('cms_preview');
    abort_if($isPreview && ! auth()->user()?->canManageCms('pages'), 404);
    $homePage = WebsitePage::query()->with(['sections.media', 'sections.draftMedia'])->where('page_key', 'home')->firstOrFail();
    abort_if(! $isPreview && ! $homePage->isPublished(), 404);
    $settings = app(WebsiteSettings::class)->all();
    $settings['seo.title'] = $homePage->seo_title ?: ($settings['seo.title'] ?? 'BEAL Consulting | Clarity in Projects. Confidence in Decisions.');
    $settings['seo.description'] = $homePage->seo_description ?: ($settings['seo.description'] ?? 'Intelligence-led strategic advisory and infrastructure consultancy across East Africa and the wider African market.');
    $siteUrl = rtrim((string) ($settings['general.site_url'] ?? config('beal.site_url')), '/');
    $sections = $homePage->sections->keyBy('section_key');
    $content = $sections->map(fn (PageSection $section): array => $isPreview ? ($section->draft_content ?? $section->content ?? []) : ($section->content ?? []))->all();
    $visible = $sections->mapWithKeys(fn (PageSection $section): array => [$section->section_key => $isPreview ? ($section->draft_enabled ?? $section->is_enabled) : $section->is_enabled])->all();
    $sectionImages = [];

    foreach (config('cms.sections') as $key => $schema) {
        $section = $sections->get($key);
        $media = $isPreview ? ($section?->draftMedia ?? $section?->media) : $section?->media;
        $sectionImages[$key] = $media?->url ?? asset($schema['image'] ?? 'assets/home-image/nairobi-skyline.jpg');
    }
    $services = WebsiteService::query()->with('media')->where('status', 'published')->orderBy('sort_order')->get();
    $services = $services->map(fn (WebsiteService $service): array => [...$service->toArray(), 'image' => $service->media?->url ?? asset(ltrim((string) $service->image_path, '/')), 'image_alt' => $service->image_alt])->all();

    return view('home-image', [
        'siteUrl' => $siteUrl,
        'content' => $content,
        'visible' => $visible,
        'sectionImages' => $sectionImages,
        'services' => $services,
        'serviceOptions' => $services,
        'settings' => $settings,
        'isPreview' => $isPreview,
        'canonicalUrl' => $siteUrl,
        'organizationJson' => json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $settings['general.legal_name'] ?? 'BEAL Consulting Ltd',
            'url' => $siteUrl,
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT),
    ]);
})->name('home');

Route::redirect('/home-image', '/', 301)->name('home.image');

Route::get('/services/{service}', function (string $service) {
    $isPreview = request()->boolean('cms_preview');
    abort_if($isPreview && ! auth()->user()?->canManageCms('services'), 404);
    $serviceDetails = WebsiteService::query()->with('media')->where('slug', $service)->when(! $isPreview, fn ($query) => $query->where('status', 'published'))->firstOrFail();
    if ($isPreview && is_array($serviceDetails->draft_content)) {
        $serviceDetails->forceFill($serviceDetails->draft_content);
        $serviceDetails->unsetRelation('media')->load('media');
    }
    $services = WebsiteService::query()->with('media')->where('status', 'published')->orderBy('sort_order')->get();
    $settings = app(WebsiteSettings::class)->all();

    $siteUrl = rtrim((string) ($settings['general.site_url'] ?? config('beal.site_url')), '/');
    $serviceArray = [...$serviceDetails->toArray(), 'image' => $serviceDetails->media?->url ?? asset(ltrim((string) $serviceDetails->image_path, '/')), 'image_alt' => $serviceDetails->image_alt];

    return view('service', [
        'service' => $serviceArray,
        'otherServices' => array_values(array_filter(
            $services->map(fn (WebsiteService $other): array => [...$other->toArray(), 'image' => $other->media?->url ?? asset(ltrim((string) $other->image_path, '/'))])->all(),
            fn (array $serviceItem): bool => $serviceItem['slug'] !== $serviceArray['slug'],
        )),
        'canonicalUrl' => $siteUrl.'/services/'.$service,
        'pageTitle' => $serviceDetails->seo_title ?: $serviceDetails->title.' | BEAL Consulting',
        'pageDescription' => $serviceDetails->seo_description ?: $serviceDetails->description,
        'settings' => $settings,
        'serviceOptions' => $services,
        'isPreview' => $isPreview,
    ]);
})->name('services.show');

Route::get('/sitemap.xml', function (WebsiteSettings $settings) {
    $url = rtrim((string) $settings->get('general.site_url', config('beal.site_url')), '/');
    $services = WebsiteService::query()->where('status', 'published')->orderBy('sort_order')->get();

    return response()->view('sitemap', ['url' => $url, 'services' => $services])->header('Content-Type', 'application/xml; charset=UTF-8');
})->name('sitemap');

Route::post('/enquiries', StoreEnquiryController::class)
    ->middleware('throttle:5,1')
    ->name('enquiries.store');

Route::prefix('admin')->as('admin.')->group(function (): void {
    Route::get('/login', [CmsAuthController::class, 'create'])->name('login');
    Route::post('/login', [CmsAuthController::class, 'store'])->middleware('throttle:5,1')->name('login.store');

    Route::middleware('cms.admin')->group(function (): void {
        Route::post('/logout', [CmsAuthController::class, 'destroy'])->name('logout');
        Route::get('/', DashboardController::class)->middleware('cms.permission:dashboard')->name('dashboard');
        Route::get('/search', SearchController::class)->name('search');

        Route::middleware('cms.permission:pages')->group(function (): void {
            Route::get('/pages', [PageController::class, 'index'])->name('pages.index');
            Route::get('/pages/{websitePage}/revisions', [PageController::class, 'revisions'])->name('pages.revisions');
            Route::post('/pages/{websitePage}/revisions/{revision}/restore', [PageController::class, 'restoreRevision'])->name('pages.revisions.restore');
            Route::get('/pages/{websitePage}/sections/{section}/revisions', [PageController::class, 'sectionRevisions'])->name('pages.sections.revisions');
            Route::post('/pages/{websitePage}/sections/{section}/revisions/{revision}/restore', [PageController::class, 'restoreSectionRevision'])->name('pages.sections.revisions.restore');
            Route::post('/pages/{websitePage}/publish', [PageController::class, 'publish'])->name('pages.publish');
            Route::get('/pages/{websitePage}', [PageController::class, 'edit'])->name('pages.edit');
            Route::put('/pages/{websitePage}', [PageController::class, 'update'])->name('pages.update');
            Route::put('/pages/{websitePage}/sections/{section}', [PageController::class, 'updateSection'])->name('pages.sections.update');
            Route::post('/pages/{websitePage}/sections/reorder', [PageController::class, 'reorderSections'])->name('pages.sections.reorder');
        });

        Route::middleware('cms.permission:services')->group(function (): void {
            Route::resource('/services', WebsiteServiceController::class)->except(['show'])->parameters(['services' => 'websiteService'])->names('services');
            Route::post('/services/{websiteService}/publish', [WebsiteServiceController::class, 'publish'])->name('services.publish');
            Route::post('/services/reorder', [WebsiteServiceController::class, 'reorder'])->name('services.reorder');
        });

        Route::middleware('cms.permission:media')->group(function (): void {
            Route::get('/media', [MediaAssetController::class, 'index'])->name('media.index');
            Route::post('/media', [MediaAssetController::class, 'store'])->middleware('throttle:20,1')->name('media.store');
            Route::patch('/media/{mediaAsset}', [MediaAssetController::class, 'update'])->name('media.update');
            Route::delete('/media/{mediaAsset}', [MediaAssetController::class, 'destroy'])->name('media.destroy');
        });

        Route::middleware('cms.permission:submissions')->group(function (): void {
            Route::get('/submissions', [ContactSubmissionController::class, 'index'])->name('submissions.index');
            Route::get('/submissions/export', [ContactSubmissionController::class, 'export'])->name('submissions.export');
            Route::get('/submissions/{contactSubmission}', [ContactSubmissionController::class, 'show'])->name('submissions.show');
            Route::patch('/submissions/{contactSubmission}', [ContactSubmissionController::class, 'update'])->name('submissions.update');
        });

        Route::middleware('cms.permission:settings')->group(function (): void {
            Route::get('/settings/{group?}', [SettingsController::class, 'edit'])->name('settings.edit');
            Route::put('/settings/{group}', [SettingsController::class, 'update'])->name('settings.update');
        });

        Route::middleware('cms.permission:users')->group(function (): void {
            Route::get('/users', [UserController::class, 'index'])->name('users.index');
            Route::post('/users', [UserController::class, 'store'])->name('users.store');
            Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update');
            Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        });

        Route::middleware('cms.permission:activity')->get('/activity', ActivityController::class)->name('activity.index');
    });
});
