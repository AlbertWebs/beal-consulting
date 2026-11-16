@extends('admin.layouts.app', ['title' => ($group === 'contact' ? 'Contact' : ucfirst($group)).' settings'])
@section('content')
<p class="cms-muted" style="margin:0">WEBSITE CONFIGURATION</p>
<h1 class="cms-title">{{ $group === 'contact' ? 'Contact details' : 'Settings' }}</h1>
<p class="cms-muted">{{ $group === 'contact' ? 'Manage the public email, phone, office location and hours shown in the website footer.' : 'Update how BEAL is presented throughout the public website.' }}</p>

<nav class="cms-actions" style="margin:18px 0" aria-label="Settings categories">
    @foreach($groups as $name => $schema)
        <a class="cms-button" @if($group === $name) aria-current="page" style="background:#c8a15d;color:#10243c" @endif href="{{ route('admin.settings.edit', ['group' => $name]) }}">{{ $name === 'contact' ? 'Contact details' : ucfirst($name) }}</a>
    @endforeach
</nav>

<div class="cms-card">
    <form method="post" action="{{ route('admin.settings.update', $group) }}">
        @csrf
        @method('PUT')
        <div class="cms-settings-fields">
            @foreach($groups[$group] as $key => $default)
                @php($label = $group === 'contact' ? ['email' => 'Public email', 'phone' => 'Public phone number', 'location' => 'Office location', 'maps_url' => 'Map link', 'business_hours' => 'Business hours'][$key] : str($key)->replace('_', ' ')->title())
                <div class="cms-field">
                    <label class="cms-label" for="setting-{{ $key }}">{{ $label }}</label>
                    @if(is_array($default))
                        <textarea class="cms-input" id="setting-{{ $key }}" rows="8" name="settings[{{ $key }}]">{{ json_encode($values[$key] ?? $default, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</textarea>
                        <small class="cms-muted">Structured setting. Edit as JSON.</small>
                    @else
                        <input class="cms-input" id="setting-{{ $key }}" name="settings[{{ $key }}]" value="{{ old('settings.'.$key, $values[$key] ?? $default) }}" @if(str_contains($key, 'email')) type="email" @elseif(str_contains($key, 'url')) type="url" @elseif(str_contains($key, 'color')) type="color" @else type="text" @endif>
                        @if($group === 'contact')
                            <small class="cms-muted">{{ ['email' => 'Shown as a clickable email address.', 'phone' => 'Shown as a tap-to-call phone number.', 'location' => 'Shown as your office location.', 'maps_url' => 'Adds a link to your location.', 'business_hours' => 'Shown with your contact details.'][$key] }}</small>
                        @endif
                    @endif
                </div>
            @endforeach
        </div>
        <button class="cms-button cms-settings-save" type="submit">Save {{ $group === 'contact' ? 'contact details' : $group.' settings' }}</button>
    </form>
</div>
@endsection
