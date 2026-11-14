<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $siteUrl = rtrim((string) config('beal.site_url'), '/');

    return view('home-image', [
        'siteUrl' => $siteUrl,
        'canonicalUrl' => $siteUrl,
        'organizationJson' => json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'BEAL Consulting Ltd',
            'url' => $siteUrl,
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT),
    ]);
})->name('home');

Route::get('/home-image', function () {
    $siteUrl = rtrim((string) config('beal.site_url'), '/');

    return view('welcome', [
        'siteUrl' => $siteUrl,
        'canonicalUrl' => $siteUrl.'/home-image',
        'organizationJson' => json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'BEAL Consulting Ltd',
            'url' => $siteUrl,
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT),
    ]);
})->name('home.image');

Route::get('/services/{service}', function (string $service) {
    $services = config('beal.services', []);
    abort_unless(array_key_exists($service, $services), 404);

    $siteUrl = rtrim((string) config('beal.site_url'), '/');
    $serviceDetails = $services[$service];

    return view('service', [
        'service' => $serviceDetails,
        'otherServices' => array_values(array_filter(
            $services,
            fn (array $serviceItem): bool => $serviceItem['slug'] !== $serviceDetails['slug'],
        )),
        'canonicalUrl' => $siteUrl.'/services/'.$service,
        'pageTitle' => $serviceDetails['title'].' | BEAL Consulting',
        'pageDescription' => $serviceDetails['description'],
    ]);
})->name('services.show');

Route::get('/sitemap.xml', function () {
    $url = rtrim((string) config('beal.site_url'), '/');

    return response()->view('sitemap', ['url' => $url])->header('Content-Type', 'application/xml; charset=UTF-8');
})->name('sitemap');
