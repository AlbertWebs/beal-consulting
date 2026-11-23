@extends('layouts.public')

@section('content')
    <section class="secondary-hero secondary-hero-about secondary-about-hero" style="--secondary-image:url('{{ $sectionImages['about_overview'] }}')">
        <div class="secondary-hero-shade"></div>
        <div class="secondary-hero-inner">
            <x-breadcrumb :items="[['label' => 'About BEAL']]" />
            <p class="eyebrow eyebrow-light"><span class="eyebrow-rule"></span>{{ $content['about_overview']['hero_eyebrow'] ?? 'ABOUT BEAL' }}</p>
            <h1>{{ $page->title }}</h1>
            <p class="secondary-hero-subtitle">{{ $content['about_overview']['hero_subtitle'] ?? 'Clarity in Projects. Confidence in Decisions.' }}</p>
        </div>
        <span class="secondary-hero-index">BEAL / 01</span>
    </section>

    @if (($visible['about_overview'] ?? true) && isset($content['about_overview']))
        <section class="secondary-intro secondary-about-overview section-pad" aria-labelledby="about-overview-title">
            <div class="secondary-index"><span>01</span><i></i><span>COMPANY OVERVIEW</span></div>
            <div class="secondary-intro-grid">
                <h2 id="about-overview-title">{{ $content['about_overview']['heading'] ?? 'An intelligence-led consultancy.' }}</h2>
                <div>
                    {!! $content['about_overview']['overview'] ?? '' !!}
                    <div class="secondary-geography">
                        <span class="secondary-geography-label">Regional ambition</span>
                        {!! $content['about_overview']['geographic_focus'] ?? '' !!}
                    </div>
                </div>
            </div>
        </section>
    @endif

    @if (($visible['about_approach'] ?? true) && isset($content['about_approach']))
        @php($approach = $content['about_approach'])
        @if (filled(strip_tags($approach['introduction'] ?? '')) || !empty($approach['items']))
            <section class="secondary-about-approach section-pad" style="order:{{ $sectionOrders['about_approach'] ?? 20 }}" aria-labelledby="about-approach-title">
                <div class="secondary-index"><span>02</span><i></i><span>OUR APPROACH</span></div>
                <div class="secondary-approach-heading">
                    <h2 id="about-approach-title">{{ $approach['heading'] ?? 'Structure for every project stage.' }}</h2>
                    <div>{!! $approach['introduction'] ?? '' !!}</div>
                </div>
                <div class="secondary-approach-grid">
                    @foreach ($approach['items'] ?? [] as $item)
                        @if (filled($item['title'] ?? '') || filled($item['description'] ?? ''))
                            <article>
                                <span>{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                <h3>{{ $item['title'] ?? '' }}</h3>
                                <p>{{ $item['description'] ?? '' }}</p>
                            </article>
                        @endif
                    @endforeach
                </div>
                @if (filled(strip_tags($approach['objective'] ?? '')))
                    <div class="secondary-approach-objective">{!! $approach['objective'] !!}</div>
                @endif
            </section>
        @endif
    @endif

    @if (($visible['about_profile'] ?? true) && isset($content['about_profile']) && (filled(strip_tags($content['about_profile']['mission'] ?? '')) || filled(strip_tags($content['about_profile']['vision'] ?? '')) || filled(strip_tags($content['about_profile']['philosophy'] ?? '')) || filled(strip_tags($content['about_profile']['commitment'] ?? '')) || !empty($content['about_profile']['values'])))
        @php($profile = $content['about_profile'])
        <section class="secondary-values secondary-about-values section-pad" style="order:{{ $sectionOrders['about_profile'] ?? 30 }}">
            <div class="secondary-index"><span>03</span><i></i><span>OUR PERSPECTIVE</span></div>
            <h2>Professional rigour.<br><em>Informed decisions.</em></h2>
            <div class="secondary-values-grid">
                @foreach (['mission' => 'Mission', 'vision' => 'Vision', 'philosophy' => 'Corporate philosophy'] as $key => $label)
                    @if (filled(strip_tags($profile[$key] ?? '')))
                        <article><span>{{ $label }}</span><div>{!! $profile[$key] !!}</div></article>
                    @endif
                @endforeach
            </div>
            @if (filled(strip_tags($profile['commitment'] ?? '')))
                <div class="secondary-commitment">
                    <div class="secondary-commitment-heading">
                        <span>Our commitment</span>
                        <h3>Clarity in projects,<br><em>confidence in decisions.</em></h3>
                    </div>
                    <div class="secondary-commitment-copy">{!! $profile['commitment'] !!}</div>
                </div>
            @endif
            @if (!empty($profile['values']))
                <div class="secondary-principles">
                    <h3>Core values</h3>
                    <ul>
                        @foreach ($profile['values'] as $value)
                            @if (is_string($value) && trim($value) !== '')
                                <li>{{ $value }}</li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            @endif
        </section>
    @endif

    @if (($visible['about_leadership'] ?? true) && isset($content['about_leadership']))
        @php($leadership = $content['about_leadership'])
        @php($teamProfiles = collect($leadership['profiles'] ?? [])->reject(fn (array $profile) => in_array(mb_strtolower(trim((string) ($profile['name'] ?? ''))), ["mark kang'ethe", 'loise manase', 'noel kibet'], true))->values())
        @if (filled(strip_tags($leadership['introduction'] ?? '')) || $teamProfiles->isNotEmpty())
            <section id="leadership" class="secondary-leadership section-pad" style="order:{{ $sectionOrders['about_leadership'] ?? 40 }}">
                <div class="secondary-index"><span>04</span><i></i><span>OUR TEAM</span></div>
                @if (filled(strip_tags($leadership['introduction'] ?? '')))
                    <div class="secondary-intro-grid"><h2>People behind<br><em>the perspective.</em></h2><div>{!! $leadership['introduction'] !!}</div></div>
                @endif
                @if ($teamProfiles->isNotEmpty())
                    @include('pages.partials.team-profile', ['profile' => $teamProfiles->first(), 'label' => 'Founder', 'founder' => true])
                @endif
                @if ($teamProfiles->count() > 1)
                    <div class="secondary-team-network">
                        <div class="secondary-team-network-heading">
                            <span>Wider team</span>
                            <h3>Meet the people<br><em>behind the work.</em></h3>
                            @if (filled(strip_tags($leadership['network_introduction'] ?? '')))
                                <div>{!! $leadership['network_introduction'] !!}</div>
                            @endif
                            <a href="{{ $leadership['team_explore_url'] ?? '#professional-experience' }}">{{ $leadership['team_explore_label'] ?? 'Explore our experience' }} <span aria-hidden="true">↗</span></a>
                        </div>
                        <div class="secondary-wider-team-grid">
                            @foreach ($teamProfiles->skip(1) as $profile)
                                @include('pages.partials.team-profile', ['profile' => $profile, 'label' => 'Team member', 'founder' => false])
                            @endforeach
                        </div>
                    </div>
                @endif
            </section>
        @endif
    @endif

    @if (($visible['about_experience'] ?? true) && isset($content['about_experience']))
        @php($experience = $content['about_experience'])
        @if (filled(strip_tags($experience['experience_introduction'] ?? '')) || !empty($experience['experience_areas']) || !empty($experience['projects']))
            <section id="professional-experience" class="secondary-experience-section section-pad" style="order:{{ $sectionOrders['about_experience'] ?? 50 }}">
                <div class="secondary-index"><span>05</span><i></i><span>PROFESSIONAL EXPERIENCE &amp; PROJECT EXPOSURE</span></div>
                <div class="secondary-experience">
                    <div class="secondary-experience-heading">
                        <h2>Experience across<br><em>the project lifecycle.</em></h2>
                        @if (filled(strip_tags($experience['experience_introduction'] ?? '')))
                            <div class="secondary-experience-intro">{!! $experience['experience_introduction'] !!}</div>
                        @endif
                    </div>
                    @if (!empty($experience['experience_areas']))
                        <div class="secondary-experience-areas">
                            <h3>Areas of experience</h3>
                            <ul>
                                @foreach ($experience['experience_areas'] as $area)
                                    @if (is_string($area) && trim($area) !== '')<li>{{ $area }}</li>@endif
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if (!empty($experience['projects']))
                        <div class="secondary-project-exposure">
                            <h3>Selected project experience of the Founding Director</h3>
                            <ul>
                                @foreach ($experience['projects'] as $project)
                                    @if (is_string($project) && trim($project) !== '')
                                        @php($projectEntry = $experienceProjectLinks->get($project))
                                        <li>
                                            @if ($projectEntry)
                                                <a class="secondary-project-link" href="{{ route('experience.show', $projectEntry->slug) }}">{{ $project }} <span aria-hidden="true">↗</span></a>
                                            @else
                                                {{ $project }}
                                            @endif
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                            @if (filled(strip_tags($experience['attribution_note'] ?? '')))
                                <div class="secondary-attribution-note">{!! $experience['attribution_note'] !!}</div>
                            @endif
                        </div>
                    @endif
                </div>
            </section>
        @endif
    @endif

    <section class="secondary-cta secondary-about-cta">
        <div class="section-pad">
            <div class="secondary-index secondary-index-light"><span>BEAL</span><i></i><span>CONTINUE EXPLORING</span></div>
            <div class="secondary-cta-layout">
                <div>
                    <h2>{{ $settings['presentation.internal_cta_heading'] ?? 'Explore the capabilities' }}<br><em>{{ $settings['presentation.internal_cta_accent'] ?? 'behind the perspective.' }}</em></h2>
                    <p>{{ $settings['presentation.internal_cta_description'] ?? 'Connect with us about complex project and infrastructure decisions.' }}</p>
                </div>
                <a class="button button-gold" href="{{ route('pages.services') }}">Explore our services <span aria-hidden="true">↗</span></a>
            </div>
        </div>
    </section>
@endsection
