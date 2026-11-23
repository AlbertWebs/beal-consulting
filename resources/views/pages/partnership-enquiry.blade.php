@extends('layouts.public')

@section('content')
    <section class="partner-enquiry-hero">
        <div class="partner-enquiry-hero-inner">
            <x-breadcrumb :items="[['label' => 'Partnerships', 'url' => route('pages.partnerships')], ['label' => 'Partner enquiry']]" />
            <p class="eyebrow"><span class="eyebrow-rule"></span>PARTNER WITH BEAL</p>
            <h1>Let’s explore<br><em>working together.</em></h1>
            <p class="partner-enquiry-lede">Tell us who you are, what your organisation brings and where you see an opportunity to work together across Kenya and Africa.</p>
        </div>
        <div class="partner-enquiry-hero-note"><span>BEAL / PARTNERSHIPS</span><b>Local insight.<br>Shared ambition.</b></div>
    </section>

    <section class="partner-enquiry-section">
        <div class="partner-enquiry-intro">
            <span class="partner-enquiry-index">01 <i></i> PARTNER ENQUIRY</span>
            <h2>A useful first<br><em>conversation starts here.</em></h2>
            <p>This short form helps our team understand your organisation, areas of expertise and the kind of partnership you have in mind. It should take about three minutes.</p>
            <p class="partner-enquiry-private">Your information will only be used to respond to this enquiry.</p>
        </div>

        <div class="partner-wizard-card">
            @if (session('success'))
                <div class="partner-form-success" role="status">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="partner-form-errors" role="alert">
                    <strong>Please review your answers.</strong>
                    <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <div class="partner-wizard-progress" aria-label="Form progress">
                <span data-partner-progress="0" class="is-active"><i>01</i><b>Your organisation</b></span>
                <span data-partner-progress="1"><i>02</i><b>Partnership fit</b></span>
                <span data-partner-progress="2"><i>03</i><b>Opportunity</b></span>
            </div>

            <form id="partnership-enquiry-form" method="post" action="{{ route('partnership.enquiry.store') }}" data-partner-wizard novalidate>
                @csrf
                <input type="hidden" name="challenge_token" value="{{ $challengeToken }}">
                <div class="partner-honeypot" aria-hidden="true"><label for="partner-website">Leave this field empty</label><input id="partner-website" name="website" type="text" tabindex="-1" autocomplete="off"></div>

                <fieldset class="partner-wizard-step is-active" data-partner-step="0">
                    <legend><span>STEP 01 / 03</span><h2>Who should we be speaking with?</h2><p>Share your contact details and a little about the organisation you represent.</p></legend>
                    <div class="partner-form-grid">
                        <label class="partner-field"><span>Your full name *</span><input name="name" type="text" autocomplete="name" maxlength="120" value="{{ old('name') }}" required>@error('name')<small>{{ $message }}</small>@enderror</label>
                        <label class="partner-field"><span>Work email *</span><input name="email" type="email" autocomplete="email" maxlength="254" value="{{ old('email') }}" required>@error('email')<small>{{ $message }}</small>@enderror</label>
                        <label class="partner-field"><span>Role or position *</span><input name="position" type="text" autocomplete="organization-title" maxlength="120" value="{{ old('position') }}" required>@error('position')<small>{{ $message }}</small>@enderror</label>
                        <label class="partner-field"><span>Organisation *</span><input name="company" type="text" autocomplete="organization" maxlength="180" value="{{ old('company') }}" required>@error('company')<small>{{ $message }}</small>@enderror</label>
                        <label class="partner-field"><span>Phone or WhatsApp</span><input name="phone" type="tel" autocomplete="tel" maxlength="50" value="{{ old('phone') }}"></label>
                        <label class="partner-field"><span>Organisation’s home country *</span><input name="home_country" type="text" autocomplete="country-name" maxlength="100" value="{{ old('home_country') }}" required>@error('home_country')<small>{{ $message }}</small>@enderror</label>
                        <label class="partner-field partner-field-wide"><span>Countries or markets where you are looking to work</span><input name="markets" type="text" maxlength="300" value="{{ old('markets') }}" placeholder="For example, Kenya, Uganda and Tanzania"></label>
                    </div>
                </fieldset>

                <fieldset class="partner-wizard-step" data-partner-step="1" hidden>
                    <legend><span>STEP 02 / 03</span><h2>Where could we complement each other?</h2><p>Help us understand your organisation’s strengths and the sectors where you work.</p></legend>
                    <div class="partner-form-grid">
                        <label class="partner-field partner-field-wide"><span>Which best describes your organisation? *</span><select name="partner_type" required><option value="">Choose one</option>@foreach(['International consultancy or engineering firm', 'Kenyan or African professional firm', 'Contractor or delivery partner', 'Development institution or public agency', 'Investor or property developer', 'Specialist service provider', 'Other'] as $option)<option value="{{ $option }}" @selected(old('partner_type') === $option)>{{ $option }}</option>@endforeach</select>@error('partner_type')<small>{{ $message }}</small>@enderror</label>
                        <div class="partner-field partner-field-wide partner-sector-field"><span>Which sectors are relevant? Select all that apply. *</span>
                            <div class="partner-sector-options">@foreach(['Commercial property', 'Infrastructure and transport', 'Energy and utilities', 'Public and institutional', 'Hospitality and tourism', 'Industrial and manufacturing', 'Housing and urban development', 'Other'] as $sector)<label><input type="checkbox" name="sectors[]" value="{{ $sector }}" @checked(in_array($sector, old('sectors', []), true))><span>{{ $sector }}</span></label>@endforeach</div>
                            @error('sectors')<small>{{ $message }}</small>@enderror
                            <small data-partner-sector-error hidden>Select at least one sector to continue.</small>
                        </div>
                        <label class="partner-field partner-field-wide"><span>What expertise, services or local capacity would your organisation bring? *</span><textarea name="capabilities" rows="4" maxlength="1800" required placeholder="Describe the experience or capability you would bring to a project team.">{{ old('capabilities') }}</textarea>@error('capabilities')<small>{{ $message }}</small>@enderror</label>
                    </div>
                </fieldset>

                <fieldset class="partner-wizard-step" data-partner-step="2" hidden>
                    <legend><span>STEP 03 / 03</span><h2>What kind of collaboration do you have in mind?</h2><p>If there is a live opportunity, share enough detail for us to understand the context.</p></legend>
                    <div class="partner-form-grid">
                        <label class="partner-field partner-field-wide"><span>What would you like to explore? *</span><select name="collaboration_interest" required><option value="">Choose one</option>@foreach(['Work together on a live project or bid', 'Build a local delivery partnership', 'Provide specialist technical support', 'Explore a longer-term framework', 'Introduce our organisation and capabilities'] as $option)<option value="{{ $option }}" @selected(old('collaboration_interest') === $option)>{{ $option }}</option>@endforeach</select>@error('collaboration_interest')<small>{{ $message }}</small>@enderror</label>
                        <label class="partner-field partner-field-wide"><span>Where are you in the process? *</span><select name="opportunity_status" required><option value="">Choose one</option>@foreach(['Active project or tender', 'Planned project pipeline', 'General partnership discussion'] as $option)<option value="{{ $option }}" @selected(old('opportunity_status') === $option)>{{ $option }}</option>@endforeach</select>@error('opportunity_status')<small>{{ $message }}</small>@enderror</label>
                        <label class="partner-field partner-field-wide"><span>Tell us briefly about the opportunity or partnership you have in mind. *</span><textarea name="opportunity_details" rows="5" maxlength="2000" required placeholder="Include the project or programme, location, timing and what a good partnership would make possible.">{{ old('opportunity_details') }}</textarea>@error('opportunity_details')<small>{{ $message }}</small>@enderror</label>
                        <label class="partner-field partner-field-wide"><span>How would you prefer us to follow up? *</span><select name="preferred_follow_up" required><option value="">Choose one</option>@foreach(['Email', 'Phone', 'WhatsApp'] as $option)<option value="{{ $option }}" @selected(old('preferred_follow_up') === $option)>{{ $option }}</option>@endforeach</select>@error('preferred_follow_up')<small>{{ $message }}</small>@enderror</label>
                    </div>
                </fieldset>

                <div class="partner-wizard-actions">
                    <button class="partner-wizard-back" type="button" data-partner-back hidden>← Back</button>
                    <span data-partner-step-count>Step 1 of 3</span>
                    <button class="button button-dark" type="button" data-partner-next>Continue <span aria-hidden="true">→</span></button>
                    <button class="button button-dark" type="submit" data-partner-submit hidden>Review and send <span aria-hidden="true">↗</span></button>
                </div>
            </form>
        </div>
    </section>

    <dialog class="partner-math-dialog" data-partner-math-dialog aria-labelledby="partner-math-title" @if ($errors->has('math_answer')) data-math-error @endif>
        <form method="dialog"><button class="partner-dialog-close" aria-label="Close verification" type="submit">×</button></form>
        <span class="partner-enquiry-index">QUICK SPAM CHECK</span>
        <h2 id="partner-math-title">One quick check.</h2>
        <p>Please solve this simple sum before we send your enquiry.</p>
        <label for="partner-math-answer">{{ $challengeLeft }} + {{ $challengeRight }} = ?</label>
        <input id="partner-math-answer" type="number" inputmode="numeric" autocomplete="off" form="partnership-enquiry-form" data-partner-math-answer>
        @error('math_answer')<small class="partner-math-error">{{ $message }}</small>@enderror
        <button class="button button-dark" type="button" data-partner-confirm>Verify and send <span aria-hidden="true">↗</span></button>
    </dialog>

    <section class="partner-enquiry-footnote"><div><span>BEAL CONSULTING LTD</span><p>Thank you for considering a partnership with our team.</p></div><a href="{{ route('pages.partnerships') }}">Back to partnerships <span aria-hidden="true">↗</span></a></section>
@endsection
