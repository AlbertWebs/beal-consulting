@extends('admin.layouts.app', ['title' => 'Enquiries'])

@section('content')
    <header class="cms-page-heading">
        <div>
            <p class="cms-page-eyebrow">COMMUNICATION</p>
            <h1 class="cms-title">Enquiries</h1>
            <p class="cms-page-intro">Website form submissions are stored securely here.</p>
        </div>
        <a class="cms-button cms-button-gold" href="{{ route('admin.submissions.export') }}">Export CSV <span aria-hidden="true">↓</span></a>
    </header>

    <section class="cms-card" aria-label="Enquiry records">
        <form method="get" class="cms-actions cms-table-filters">
            <label class="cms-table-filter-search">
                <span class="cms-visually-hidden">Search enquiries by name or email</span>
                <input class="cms-input" name="q" value="{{ request('q') }}" placeholder="Search name or email">
            </label>
            <label class="cms-table-filter-status">
                <span class="cms-visually-hidden">Filter enquiries by status</span>
                <select class="cms-select" name="status" aria-label="Filter enquiries by status">
                    <option value="">All statuses</option>
                    @foreach(['new', 'read', 'in_progress', 'handled', 'spam'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ str($status)->replace('_', ' ')->title() }}</option>
                    @endforeach
                </select>
            </label>
            <button class="cms-button" type="submit">Filter enquiries</button>
        </form>

        <div class="cms-table-scroll">
            <table class="cms-table">
                <caption class="cms-table-caption">Enquiries received from the website contact form</caption>
                <thead>
                    <tr><th scope="col">Received</th><th scope="col">Contact</th><th scope="col">Company</th><th scope="col">Service</th><th scope="col">Status</th><th scope="col"><span class="cms-visually-hidden">Actions</span></th></tr>
                </thead>
                <tbody>
                    @forelse($submissions as $submission)
                        <tr>
                            <td data-label="Received"><time class="cms-page-updated" datetime="{{ $submission->created_at->toIso8601String() }}">{{ $submission->created_at->format('M j, Y') }}</time><span class="cms-table-subtext">{{ $submission->created_at->format('g:i A') }}</span></td>
                            <td data-label="Contact"><strong>{{ $submission->name }}</strong><span class="cms-table-subtext"><a href="mailto:{{ $submission->email }}">{{ $submission->email }}</a></span></td>
                            <td data-label="Company">{{ $submission->company ?: '-' }}</td>
                            <td data-label="Service">{{ $submission->service ?: '-' }}</td>
                            <td data-label="Status"><span class="cms-status-pill cms-status-{{ $submission->status }}">{{ str($submission->status)->replace('_', ' ')->title() }}</span></td>
                            <td data-label="Action"><a class="cms-table-action" href="{{ route('admin.submissions.show', $submission) }}">Read <span aria-hidden="true">↗</span></a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="cms-table-empty">No enquiries match these filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="cms-table-pagination">{{ $submissions->links() }}</div>
    </section>
@endsection
