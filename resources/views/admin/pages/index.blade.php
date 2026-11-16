@extends('admin.layouts.app', ['title' => 'Pages'])
@section('content')
<div style="margin-bottom:20px"><p class="cms-muted" style="margin:0 0 5px">CONTENT MANAGEMENT</p><h1 class="cms-title">Pages</h1><p class="cms-muted">Edit copy, imagery, visibility, SEO and publishing status for the website.</p></div>
<div class="cms-card"><table class="cms-table"><thead><tr><th>Page</th><th>Visibility</th><th>Status</th><th>Last updated</th><th></th></tr></thead><tbody>@foreach($pages as $page)<tr><td><strong>{{ $page->title }}</strong><br><span class="cms-muted">{{ $page->slug }}</span></td><td>{{ $page->show_in_navigation ? 'In navigation' : 'Hidden' }}</td><td>{{ ucfirst($page->status) }}</td><td>{{ $page->updater?->name ?? '—' }} · {{ $page->updated_at->diffForHumans() }}</td><td><a class="cms-button" href="{{ route('admin.pages.edit',$page) }}">Edit</a></td></tr>@endforeach</tbody></table></div>
@endsection
