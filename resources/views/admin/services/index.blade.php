@extends('admin.layouts.app', ['title' => 'Services'])
@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px"><div><p class="cms-muted" style="margin:0">CONTENT MANAGEMENT</p><h1 class="cms-title">Services</h1><p class="cms-muted">Manage service cards and detail pages shown on the website.</p></div><a class="cms-button cms-button-gold" href="{{ route('admin.services.create') }}">Add service +</a></div>
<div class="cms-card"><table class="cms-table"><thead><tr><th>Service</th><th>Slug</th><th>Status</th><th>Order</th><th></th></tr></thead><tbody>@foreach($services as $service)<tr><td><strong>{{ $service->number }} · {{ $service->title }}</strong></td><td>{{ $service->slug }}</td><td>{{ ucfirst($service->status) }}</td><td>{{ $service->sort_order }}</td><td><a class="cms-button" href="{{ route('admin.services.edit',$service) }}">Edit</a></td></tr>@endforeach</tbody></table></div>
@endsection
