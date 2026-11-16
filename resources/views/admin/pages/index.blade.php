@extends('admin.layouts.app', ['title' => 'Pages'])

@section('content')
    <header class="cms-page-heading">
        <div>
            <p class="cms-page-eyebrow">CONTENT MANAGEMENT</p>
            <h1 class="cms-title">Pages</h1>
            <p class="cms-page-intro">Manage page content, navigation visibility, SEO and publishing status.</p>
        </div>
    </header>

    <section class="cms-card cms-page-table-card" aria-label="Website pages">
        <div class="cms-table-card-heading">
            <h2>Website pages</h2>
            <span>{{ number_format($pages->count()) }} {{ \Illuminate\Support\Str::plural('page', $pages->count()) }}</span>
        </div>
        <div class="cms-pages-toolbar" data-page-filters hidden>
            <label class="cms-pages-search">
                <span class="cms-visually-hidden">Search pages by title or URL</span>
                <svg aria-hidden="true" viewBox="0 0 24 24" focusable="false"><circle cx="10.8" cy="10.8" r="6.3"></circle><path d="m15.5 15.5 4.2 4.2"></path></svg>
                <input type="search" data-page-search placeholder="Search pages by title or URL" autocomplete="off" aria-controls="pages-table-body">
            </label>
            <label class="cms-pages-filter">
                <span class="cms-visually-hidden">Filter by publishing status</span>
                <select data-page-status aria-label="Filter by publishing status">
                    <option value="all">All statuses</option>
                    <option value="published">Published</option>
                    <option value="draft">Draft</option>
                </select>
            </label>
            <label class="cms-pages-filter">
                <span class="cms-visually-hidden">Filter by navigation visibility</span>
                <select data-page-navigation aria-label="Filter by navigation visibility">
                    <option value="all">Any visibility</option>
                    <option value="visible">Visible in navigation</option>
                    <option value="hidden">Hidden from navigation</option>
                </select>
            </label>
            <button class="cms-pages-reset" type="button" data-page-reset hidden>Clear filters</button>
        </div>
        <div class="cms-pages-result-bar" data-page-result-bar hidden>
            <span data-page-result-count aria-live="polite"></span>
        </div>
        <div class="cms-table-frame">
            <table class="cms-table cms-pages-table">
                <caption class="cms-table-caption">Website pages, navigation visibility, publishing status, last update and edit action</caption>
                <thead>
                    <tr>
                        <th scope="col">Page</th>
                        <th scope="col">Navigation</th>
                        <th scope="col">Status</th>
                        <th scope="col">Last updated</th>
                        <th scope="col"><span class="cms-visually-hidden">Actions</span></th>
                    </tr>
                </thead>
                <tbody id="pages-table-body">
                    @forelse($pages as $page)
                        <tr data-page-row data-page-search="{{ $page->title.' '.$page->slug }}" data-page-status="{{ $page->status }}" data-page-navigation="{{ $page->show_in_navigation ? 'visible' : 'hidden' }}">
                            <td data-label="Page">
                                <div class="cms-page-record">
                                    <strong>{{ $page->title }}</strong>
                                    <span>{{ $page->slug === '/' ? 'Homepage' : (str_starts_with($page->slug, '/') ? $page->slug : '/'.$page->slug) }}</span>
                                </div>
                            </td>
                            <td data-label="Navigation">
                                <span @class(['cms-table-badge', 'cms-table-badge-visible' => $page->show_in_navigation, 'cms-table-badge-muted' => ! $page->show_in_navigation])>
                                    {{ $page->show_in_navigation ? 'Visible' : 'Hidden' }}
                                </span>
                            </td>
                            <td data-label="Status">
                                <span @class(['cms-status-pill', 'cms-status-published' => $page->status === 'published', 'cms-status-draft' => $page->status === 'draft'])>{{ ucfirst($page->status) }}</span>
                            </td>
                            <td data-label="Last updated">
                                <time class="cms-page-updated" datetime="{{ $page->updated_at->toIso8601String() }}" title="{{ $page->updated_at->format('F j, Y g:i A') }}">{{ $page->updated_at->diffForHumans() }}</time>
                                @if($page->updater)
                                    <span class="cms-page-updater">by {{ $page->updater->name }}</span>
                                @endif
                            </td>
                            <td data-label="Action">
                                <a class="cms-table-action" href="{{ route('admin.pages.edit', $page) }}" aria-label="Edit {{ $page->title }}">Edit page <span aria-hidden="true">↗</span></a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="cms-table-empty">No pages are available yet.</td></tr>
                    @endforelse
                    @if($pages->isNotEmpty())
                        <tr data-page-filter-empty hidden><td colspan="5" class="cms-table-empty">No pages match these filters. Clear the filters to see all pages.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        (() => {
            const filters = document.querySelector('[data-page-filters]');
            const resultBar = document.querySelector('[data-page-result-bar]');
            const searchInput = document.querySelector('[data-page-search]');
            const statusFilter = document.querySelector('[data-page-status]');
            const navigationFilter = document.querySelector('[data-page-navigation]');
            const resetButton = document.querySelector('[data-page-reset]');
            const resultCount = document.querySelector('[data-page-result-count]');
            const rows = [...document.querySelectorAll('[data-page-row]')];
            const emptyState = document.querySelector('[data-page-filter-empty]');

            if (!filters || !resultBar || !searchInput || !statusFilter || !navigationFilter || !resetButton || !resultCount || !emptyState) {
                return;
            }

            const updatePageList = () => {
                const query = searchInput.value.trim().toLocaleLowerCase();
                const status = statusFilter.value;
                const navigation = navigationFilter.value;
                let visibleCount = 0;

                rows.forEach((row) => {
                    const matchesQuery = row.dataset.pageSearch.toLocaleLowerCase().includes(query);
                    const matchesStatus = status === 'all' || row.dataset.pageStatus === status;
                    const matchesNavigation = navigation === 'all' || row.dataset.pageNavigation === navigation;
                    const isVisible = matchesQuery && matchesStatus && matchesNavigation;

                    row.hidden = !isVisible;
                    visibleCount += Number(isVisible);
                });

                emptyState.hidden = visibleCount > 0;
                resultCount.textContent = `Showing ${visibleCount} of ${rows.length} ${rows.length === 1 ? 'page' : 'pages'}`;
                resetButton.hidden = !query && status === 'all' && navigation === 'all';
            };

            filters.hidden = false;
            resultBar.hidden = false;
            searchInput.addEventListener('input', updatePageList);
            statusFilter.addEventListener('change', updatePageList);
            navigationFilter.addEventListener('change', updatePageList);
            resetButton.addEventListener('click', () => {
                searchInput.value = '';
                statusFilter.value = 'all';
                navigationFilter.value = 'all';
                updatePageList();
                searchInput.focus();
            });

            updatePageList();
        })();
    </script>
@endpush
