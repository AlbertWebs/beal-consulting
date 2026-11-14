<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ $url }}/</loc>
        <changefreq>monthly</changefreq>
    </url>
    @foreach ($services as $service)
        <url>
            <loc>{{ $url }}/services/{{ $service['slug'] }}</loc>
            <changefreq>monthly</changefreq>
        </url>
    @endforeach
</urlset>
