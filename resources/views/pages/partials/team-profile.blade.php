@php
    $profileName = (string) ($profile['name'] ?? '');
    $nameParts = preg_split('/\s+/', trim($profileName)) ?: [];
    $profileInitials = mb_strtoupper(mb_substr($nameParts[0] ?? 'T', 0, 1).(count($nameParts) > 1 ? mb_substr(end($nameParts), 0, 1) : ''));
    $bio = is_array($profile['bio'] ?? null) ? $profile['bio'] : (filled($profile['bio'] ?? '') ? [strip_tags($profile['bio'])] : []);
    $profileSlug = filled($profile['slug'] ?? null) ? \Illuminate\Support\Str::slug($profile['slug']) : \Illuminate\Support\Str::slug($profileName);
@endphp

<article class="secondary-team-card {{ $founder ? 'secondary-team-card-founder' : 'secondary-team-card-member' }}">
    @if (filled($profile['image'] ?? ''))
        <div class="secondary-team-portrait"><img src="{{ $profile['image'] }}" alt="{{ $profile['image_alt'] ?? 'Portrait of '.$profileName }}" loading="lazy"></div>
    @else
        <div class="secondary-team-portrait secondary-team-portrait-placeholder" aria-hidden="true"><span>{{ $profileInitials }}</span></div>
    @endif
    <div class="secondary-team-card-content">
        <div class="secondary-team-card-heading">
            <span class="secondary-team-label">{{ $label }}</span>
            <h3>{{ $profileName }}</h3>
            @if (filled($profile['role'] ?? '') && ($profile['role'] ?? '') !== ($profile['designation'] ?? null))
                <p>{{ $profile['role'] }}</p>
            @endif
        </div>
        @if ($founder && collect($bio)->contains(fn ($paragraph) => filled($paragraph)))
            <div class="secondary-team-bio">
                @foreach (collect($bio)->filter(fn ($paragraph) => filled($paragraph))->take(3) as $paragraph)
                    <p>{{ $paragraph }}</p>
                @endforeach
            </div>
        @endif
        <a class="text-link text-link-navy" href="{{ route('team.show', ['slug' => $profileSlug]) }}">View profile <span aria-hidden="true">↗</span></a>
    </div>
</article>
