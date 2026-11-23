<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @if($indexingEnabled)
    <url>
        <loc>{{ $url }}/</loc>
        <changefreq>monthly</changefreq>
    </url>
    <url>
        <loc>{{ $url }}/partnerships</loc>
        <changefreq>monthly</changefreq>
    </url>
    @foreach ($services as $service)
        <url>
            <loc>{{ $url }}/services/{{ $service['slug'] }}</loc>
            <changefreq>monthly</changefreq>
        </url>
    @endforeach
    @foreach ($pages as $page)
        <url><loc>{{ $url }}/{{ $page->slug }}</loc><changefreq>monthly</changefreq></url>
    @endforeach
    @foreach ($entries as $entry)
        @php($entryPath = $entry->type === 'sector' ? 'sectors' : ($entry->type === 'insight' ? 'insights' : 'experience'))
        <url><loc>{{ $url }}/{{ $entryPath }}/{{ $entry->slug }}</loc><lastmod>{{ ($entry->updated_at ?? now())->toAtomString() }}</lastmod><changefreq>monthly</changefreq></url>
    @endforeach
    @foreach ($teamProfiles as $profile)
        @php($profileSlug = filled($profile['slug'] ?? null) ? \Illuminate\Support\Str::slug($profile['slug']) : \Illuminate\Support\Str::slug($profile['name']))
        <url><loc>{{ $url }}/team/{{ $profileSlug }}</loc><changefreq>monthly</changefreq></url>
    @endforeach
    @endif
</urlset>
