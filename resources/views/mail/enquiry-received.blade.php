<h1>New website enquiry</h1>

<p><strong>Name:</strong> {{ $enquiry['name'] }}</p>
<p><strong>Email:</strong> {{ $enquiry['email'] }}</p>

@if ($enquiry['company'])
    <p><strong>Company:</strong> {{ $enquiry['company'] }}</p>
@endif

@if ($enquiry['service'])
    <p><strong>Service area:</strong> {{ $enquiry['service'] }}</p>
@endif

<h2>Enquiry</h2>
<div style="white-space: pre-wrap">{{ $enquiry['message'] }}</div>
