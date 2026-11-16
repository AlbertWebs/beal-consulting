@extends('admin.layouts.app', ['title' => 'Dashboard'])

@section('content')
    <header class="cms-dashboard-heading">
        <div>
            <p class="cms-dashboard-eyebrow">YOUR WORKSPACE</p>
            <h1 class="cms-title">Good {{ now()->format('H') < 12 ? 'morning' : 'afternoon' }}, {{ auth()->user()->name }}</h1>
            <p class="cms-dashboard-intro">A clear overview of your website content and activity.</p>
        </div>
        <a class="cms-button cms-button-gold cms-dashboard-primary-action" href="{{ route('admin.pages.edit', \App\Models\WebsitePage::where('page_key', 'home')->firstOrFail()) }}">
            Edit homepage <span aria-hidden="true">↗</span>
        </a>
    </header>

    <section class="cms-grid cms-dashboard-metrics" aria-label="Website metrics">
        @foreach(['Pages' => $counts['pages'], 'Published pages' => $counts['published'], 'Draft pages' => $counts['drafts'], 'Services' => $counts['services'], 'Sectors' => $counts['sectors'], 'Insights' => $counts['insights'], 'Experience records' => $counts['projects'], 'Media assets' => $counts['media'], 'Enquiries' => $counts['submissions']] as $label => $count)
            <article class="cms-card cms-dashboard-metric">
                <h2>{{ $label }}</h2>
                <p>{{ number_format($count) }}</p>
            </article>
        @endforeach
    </section>

    <section id="dashboard-charts" class="cms-dashboard-charts" aria-label="Website data visualizations">
        <article class="cms-card cms-dashboard-chart-card">
            <div class="cms-dashboard-section-heading">
                <div>
                    <p class="cms-dashboard-eyebrow">CONTENT INVENTORY</p>
                    <h2>Records by type</h2>
                </div>
                <span class="cms-dashboard-chart-caption">Current totals</span>
            </div>
            <ul class="cms-dashboard-bar-chart">
                @foreach($contentChart as $item)
                    <li>
                        <div class="cms-dashboard-bar-meta"><span>{{ $item['label'] }}</span><strong>{{ number_format($item['count']) }}</strong></div>
                        <div class="cms-dashboard-bar-track" aria-hidden="true"><span style="width: {{ $item['percentage'] }}%"></span></div>
                    </li>
                @endforeach
            </ul>
        </article>

        <article class="cms-card cms-dashboard-chart-card">
            <div class="cms-dashboard-section-heading">
                <div>
                    <p class="cms-dashboard-eyebrow">PAGES</p>
                    <h2>Publishing status</h2>
                </div>
            </div>
            <div class="cms-dashboard-publishing-layout">
                <div class="cms-dashboard-donut" role="img" aria-label="{{ $publishingChart['published'] }} published, {{ $publishingChart['drafts'] }} draft, {{ $publishingChart['unpublished'] }} unpublished pages" style="--published-end: {{ $publishingChart['publishedEndAngle'] }}deg; --draft-end: {{ $publishingChart['draftEndAngle'] }}deg">
                    <div><strong>{{ number_format($publishingChart['total']) }}</strong><span>Total pages</span></div>
                </div>
                <ul class="cms-dashboard-chart-legend">
                    <li><span class="cms-dashboard-legend-dot is-published" aria-hidden="true"></span><span>Published</span><strong>{{ number_format($publishingChart['published']) }}</strong></li>
                    <li><span class="cms-dashboard-legend-dot is-draft" aria-hidden="true"></span><span>Draft</span><strong>{{ number_format($publishingChart['drafts']) }}</strong></li>
                    <li><span class="cms-dashboard-legend-dot is-unpublished" aria-hidden="true"></span><span>Unpublished</span><strong>{{ number_format($publishingChart['unpublished']) }}</strong></li>
                </ul>
            </div>
            @if($publishingChart['total'] === 0)
                <p class="cms-dashboard-chart-empty">Page status data will appear here when pages are added.</p>
            @endif
        </article>
    </section>

    <section class="cms-dashboard-overview" aria-label="Recent website updates">
        <article class="cms-card cms-dashboard-pages">
            <div class="cms-dashboard-section-heading">
                <div>
                    <p class="cms-dashboard-eyebrow">CONTENT</p>
                    <h2>Recently updated pages</h2>
                </div>
                <a class="cms-dashboard-view-all" href="{{ route('admin.pages.index') }}">All pages <span aria-hidden="true">↗</span></a>
            </div>
            <div class="cms-table-scroll">
                <table class="cms-table cms-dashboard-table">
                    <thead>
                        <tr><th scope="col">Page</th><th scope="col">Status</th><th scope="col">Updated</th></tr>
                    </thead>
                    <tbody>
                        @forelse($recentPages as $page)
                            <tr>
                                <td data-label="Page"><a href="{{ route('admin.pages.edit', $page) }}">{{ $page->title }} <span aria-hidden="true">↗</span></a></td>
                                <td data-label="Status"><span @class(['cms-status-pill', 'cms-status-published' => $page->status === 'published', 'cms-status-draft' => $page->status === 'draft'])>{{ ucfirst($page->status) }}</span></td>
                                <td data-label="Updated">{{ $page->updated_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="cms-dashboard-empty">No pages yet. Add your first page to get started.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>

        <article class="cms-card cms-dashboard-activity">
            <div class="cms-dashboard-section-heading">
                <div>
                    <p class="cms-dashboard-eyebrow">TEAM</p>
                    <h2>Recent activity</h2>
                </div>
                @if(auth()->user()->canManageCms('activity'))
                    <a class="cms-dashboard-view-all" href="{{ route('admin.activity.index') }}">Activity log <span aria-hidden="true">↗</span></a>
                @endif
            </div>
            @forelse($activities as $activity)
                <div class="cms-dashboard-activity-item">
                    <span class="cms-dashboard-activity-dot" aria-hidden="true"></span>
                    <div>
                        <p>{{ $activity->summary }}</p>
                        <span class="cms-dashboard-activity-meta">{{ $activity->user?->name ?? 'System' }} <span aria-hidden="true">·</span> {{ $activity->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            @empty
                <div class="cms-dashboard-activity-empty">
                    <span aria-hidden="true">◷</span>
                    <p>No activity recorded yet.</p>
                    <span>Changes made in the CMS will appear here.</span>
                </div>
            @endforelse
        </article>
    </section>

    <section class="cms-card cms-dashboard-quick-actions" aria-labelledby="cms-quick-actions-title">
        <div class="cms-dashboard-section-heading">
            <div>
                <p class="cms-dashboard-eyebrow">SHORTCUTS</p>
                <h2 id="cms-quick-actions-title">Quick actions</h2>
            </div>
        </div>
        <div class="cms-dashboard-action-grid">
            <a class="cms-dashboard-action" href="{{ route('admin.pages.index') }}"><span>Manage pages</span><b aria-hidden="true">↗</b></a>
            <a class="cms-dashboard-action" href="{{ route('admin.services.index') }}"><span>Manage services</span><b aria-hidden="true">↗</b></a>
            @if(auth()->user()->canManageCms('content'))
                <a class="cms-dashboard-action" href="{{ route('admin.content.index', ['type' => 'sector']) }}"><span>Manage sectors</span><b aria-hidden="true">↗</b></a>
                <a class="cms-dashboard-action" href="{{ route('admin.content.index', ['type' => 'insight']) }}"><span>Publish insights</span><b aria-hidden="true">↗</b></a>
                <a class="cms-dashboard-action" href="{{ route('admin.content.index', ['type' => 'project']) }}"><span>Manage experience</span><b aria-hidden="true">↗</b></a>
            @endif
            <a class="cms-dashboard-action" href="{{ route('admin.media.index') }}"><span>Upload media</span><b aria-hidden="true">↗</b></a>
            @if(auth()->user()->canManageCms('submissions'))
                <a class="cms-dashboard-action" href="{{ route('admin.submissions.index') }}"><span>View enquiries</span><b aria-hidden="true">↗</b></a>
            @endif
            @if(auth()->user()->canManageCms('settings'))
                <a class="cms-dashboard-action" href="{{ route('admin.settings.edit') }}"><span>Website settings</span><b aria-hidden="true">↗</b></a>
            @endif
        </div>
    </section>

    <section class="cms-card cms-dashboard-health" aria-labelledby="cms-content-health-title">
        <div class="cms-dashboard-section-heading">
            <div>
                <p class="cms-dashboard-eyebrow">SITE CHECK</p>
                <h2 id="cms-content-health-title">Content health</h2>
            </div>
        </div>
        <div class="cms-dashboard-health-grid">
            <div @class(['cms-dashboard-health-item', 'is-good' => $health['missing_seo'] === 0, 'is-attention' => $health['missing_seo'] > 0])>
                <strong>{{ number_format($health['missing_seo']) }}</strong>
                <p>{{ $health['missing_seo'] === 0 ? 'All pages have an SEO description' : 'Pages missing an SEO description' }}</p>
            </div>
            <div @class(['cms-dashboard-health-item', 'is-good' => $health['missing_alt'] === 0, 'is-attention' => $health['missing_alt'] > 0])>
                <strong>{{ number_format($health['missing_alt']) }}</strong>
                <p>{{ $health['missing_alt'] === 0 ? 'All images have alternative text' : 'Images missing alternative text' }}</p>
            </div>
        </div>
    </section>
@endsection
