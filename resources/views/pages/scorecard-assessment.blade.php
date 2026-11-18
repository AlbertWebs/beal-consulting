@extends('layouts.public')

@section('content')
    <section class="secondary-hero secondary-hero-about" style="--secondary-image:url('{{ $bannerImage }}')">
        <div class="secondary-hero-shade"></div>
        <div class="secondary-hero-inner">
            <x-breadcrumb :items="[['label' => 'Services', 'url' => route('pages.services')], ['label' => 'Project Health Scorecard', 'url' => route('services.show', ['service' => 'governance-risk-performance']).'#service-scorecard'], ['label' => 'Request an assessment']]" />
            <p class="eyebrow eyebrow-light"><span class="eyebrow-rule"></span>PROJECT HEALTH ASSESSMENT</p>
            <h1>Tell us about<br>your project.</h1>
            <p class="secondary-hero-subtitle">A few details will help us understand the project and prepare for a useful first conversation.</p>
        </div>
        <span class="secondary-hero-index">BEAL / SCORECARD</span>
    </section>

    <section class="scorecard-request-section section-pad">
        <div class="scorecard-request-intro">
            <div class="secondary-index"><span>BEAL</span><i></i><span>ASSESSMENT REQUEST</span></div>
            <h2>Start with the<br><em>project context.</em></h2>
            <p>This request helps us understand your project’s stage, current pressures and the questions you want the assessment to address. BEAL will follow up to discuss scope, information needs and next steps.</p>
            <p class="scorecard-request-note">Share high-level information only. You can discuss detailed project documents with the BEAL team later.</p>
        </div>

        <div class="scorecard-wizard-card">
            <div class="scorecard-wizard-heading">
                <div>
                    <span class="scorecard-wizard-kicker">PROJECT HEALTH SCORECARD</span>
                    <h2>Assessment request</h2>
                </div>
                <span class="scorecard-wizard-duration">3 steps · about 3 minutes</span>
            </div>

            <div class="scorecard-wizard-progress" aria-live="polite">
                <span data-assessment-step-label>Step 1 of 3 · Project details</span>
                <span data-assessment-progress-text>33%</span>
                <div class="scorecard-wizard-progress-track"><span data-assessment-progress-bar></span></div>
            </div>

            @if ($errors->any())
                <div class="scorecard-form-feedback is-error" role="alert">Please review the highlighted fields and try again.</div>
            @endif
            @if (session('success'))
                <div class="scorecard-form-feedback is-success" role="status">{{ session('success') }}</div>
            @endif

            <form action="{{ route('scorecard.assessment.store') }}" method="post" data-assessment-form>
                @csrf
                <noscript><p class="scorecard-form-feedback">All assessment steps are shown below. Complete the fields and use the submit button at the end.</p></noscript>
                <div class="scorecard-honeypot" aria-hidden="true"><label for="assessment-website">Leave this field empty</label><input id="assessment-website" name="website" tabindex="-1" autocomplete="off"></div>

                <fieldset class="scorecard-wizard-step" data-assessment-step>
                    <legend>Project details</legend>
                    <p class="scorecard-step-description">Tell us what the project is and where it stands today.</p>
                    <div class="scorecard-form-grid">
                        <div class="scorecard-form-field scorecard-form-field-full" data-assessment-field="project_name">
                            <label for="assessment-project-name">Project name <span aria-hidden="true">*</span></label>
                            <input id="assessment-project-name" name="project_name" type="text" maxlength="180" required autocomplete="off" value="{{ old('project_name') }}" aria-describedby="assessment-project-name-error">
                            <span class="scorecard-field-error" id="assessment-project-name-error" data-assessment-error="project_name">@error('project_name'){{ $message }}@enderror</span>
                        </div>
                        <div class="scorecard-form-field" data-assessment-field="project_type">
                            <label for="assessment-project-type">Project type <span aria-hidden="true">*</span></label>
                            <select id="assessment-project-type" name="project_type" required aria-describedby="assessment-project-type-error">
                                <option value="">Choose a project type</option>
                                @foreach (['Commercial property', 'Residential development', 'Infrastructure & transport', 'Hospitality', 'Institutional', 'Energy & utilities', 'Industrial & manufacturing', 'Other'] as $projectType)
                                    <option value="{{ $projectType }}" @selected(old('project_type') === $projectType)>{{ $projectType }}</option>
                                @endforeach
                            </select>
                            <span class="scorecard-field-error" id="assessment-project-type-error" data-assessment-error="project_type">@error('project_type'){{ $message }}@enderror</span>
                        </div>
                        <div class="scorecard-form-field" data-assessment-field="location">
                            <label for="assessment-location">Project location <span aria-hidden="true">*</span></label>
                            <input id="assessment-location" name="location" type="text" maxlength="180" placeholder="County, city or country" required value="{{ old('location') }}" aria-describedby="assessment-location-error">
                            <span class="scorecard-field-error" id="assessment-location-error" data-assessment-error="location">@error('location'){{ $message }}@enderror</span>
                        </div>
                        <div class="scorecard-form-field scorecard-form-field-full" data-assessment-field="project_stage">
                            <label for="assessment-project-stage">Current project stage <span aria-hidden="true">*</span></label>
                            <select id="assessment-project-stage" name="project_stage" required aria-describedby="assessment-project-stage-error">
                                <option value="">Choose the closest stage</option>
                                @foreach (['Feasibility & planning', 'Design & approvals', 'Procurement & mobilisation', 'Construction', 'Commissioning & handover', 'Operational or recovery'] as $projectStage)
                                    <option value="{{ $projectStage }}" @selected(old('project_stage') === $projectStage)>{{ $projectStage }}</option>
                                @endforeach
                            </select>
                            <span class="scorecard-field-error" id="assessment-project-stage-error" data-assessment-error="project_stage">@error('project_stage'){{ $message }}@enderror</span>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="scorecard-wizard-step" data-assessment-step>
                    <legend>Project health today</legend>
                    <p class="scorecard-step-description">A broad indication is enough. You can explain the detail in the next fields.</p>
                    <div class="scorecard-form-grid">
                        <div class="scorecard-form-field" data-assessment-field="budget_range">
                            <label for="assessment-budget-range">Approved project budget <span class="scorecard-optional">Optional</span></label>
                            <select id="assessment-budget-range" name="budget_range" aria-describedby="assessment-budget-range-error">
                                <option value="">Prefer not to say</option>
                                @foreach (['Below KES 50 million', 'KES 50–250 million', 'KES 250 million–1 billion', 'Above KES 1 billion'] as $budgetRange)
                                    <option value="{{ $budgetRange }}" @selected(old('budget_range') === $budgetRange)>{{ $budgetRange }}</option>
                                @endforeach
                            </select>
                            <span class="scorecard-field-error" id="assessment-budget-range-error" data-assessment-error="budget_range">@error('budget_range'){{ $message }}@enderror</span>
                        </div>
                        <div class="scorecard-form-field" data-assessment-field="cost_position">
                            <label for="assessment-cost-position">Cost position <span aria-hidden="true">*</span></label>
                            <select id="assessment-cost-position" name="cost_position" required aria-describedby="assessment-cost-position-error">
                                <option value="">Choose one</option>
                                @foreach (['Tracking to plan', 'Forecast overrun or cost pressure', 'No reliable cost baseline', 'Unsure'] as $costPosition)
                                    <option value="{{ $costPosition }}" @selected(old('cost_position') === $costPosition)>{{ $costPosition }}</option>
                                @endforeach
                            </select>
                            <span class="scorecard-field-error" id="assessment-cost-position-error" data-assessment-error="cost_position">@error('cost_position'){{ $message }}@enderror</span>
                        </div>
                        <div class="scorecard-form-field scorecard-form-field-full" data-assessment-field="schedule_position">
                            <label for="assessment-schedule-position">Schedule position <span aria-hidden="true">*</span></label>
                            <select id="assessment-schedule-position" name="schedule_position" required aria-describedby="assessment-schedule-position-error">
                                <option value="">Choose one</option>
                                @foreach (['Tracking to plan', 'At risk of delay', 'Behind schedule', 'No reliable baseline', 'Unsure'] as $schedulePosition)
                                    <option value="{{ $schedulePosition }}" @selected(old('schedule_position') === $schedulePosition)>{{ $schedulePosition }}</option>
                                @endforeach
                            </select>
                            <span class="scorecard-field-error" id="assessment-schedule-position-error" data-assessment-error="schedule_position">@error('schedule_position'){{ $message }}@enderror</span>
                        </div>
                        <div class="scorecard-form-field scorecard-form-field-full" data-assessment-field="concerns">
                            <span class="scorecard-field-label">Which areas would you like us to look at? <span aria-hidden="true">*</span></span>
                            <div class="scorecard-concern-grid">
                                @foreach (['Cost', 'Schedule', 'Quality', 'Procurement', 'Commercial', 'HSE', 'Governance', 'Delivery'] as $concern)
                                    <label class="scorecard-check-option"><input type="checkbox" name="concerns[]" value="{{ $concern }}" @checked(in_array($concern, old('concerns', []), true))><span>{{ $concern }}</span></label>
                                @endforeach
                            </div>
                            <span class="scorecard-field-error" data-assessment-error="concerns">@error('concerns'){{ $message }}@enderror</span>
                        </div>
                        <div class="scorecard-form-field scorecard-form-field-full" data-assessment-field="project_context">
                            <label for="assessment-project-context">What is happening on the project? <span aria-hidden="true">*</span></label>
                            <textarea id="assessment-project-context" name="project_context" rows="4" maxlength="2000" required placeholder="For example: a forecast overrun, a delayed critical package, emerging quality issues or a need for an independent review." aria-describedby="assessment-project-context-error">{{ old('project_context') }}</textarea>
                            <span class="scorecard-field-error" id="assessment-project-context-error" data-assessment-error="project_context">@error('project_context'){{ $message }}@enderror</span>
                        </div>
                        <div class="scorecard-form-field scorecard-form-field-full" data-assessment-field="assessment_goal">
                            <label for="assessment-goal">What would you like the assessment to help clarify? <span aria-hidden="true">*</span></label>
                            <textarea id="assessment-goal" name="assessment_goal" rows="3" maxlength="1500" required aria-describedby="assessment-goal-error">{{ old('assessment_goal') }}</textarea>
                            <span class="scorecard-field-error" id="assessment-goal-error" data-assessment-error="assessment_goal">@error('assessment_goal'){{ $message }}@enderror</span>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="scorecard-wizard-step" data-assessment-step>
                    <legend>Your contact details</legend>
                    <p class="scorecard-step-description">We’ll use these details to follow up about the project and the next steps.</p>
                    <div class="scorecard-form-grid">
                        <div class="scorecard-form-field" data-assessment-field="name">
                            <label for="assessment-name">Your name <span aria-hidden="true">*</span></label>
                            <input id="assessment-name" name="name" type="text" maxlength="120" autocomplete="name" required value="{{ old('name') }}" aria-describedby="assessment-name-error">
                            <span class="scorecard-field-error" id="assessment-name-error" data-assessment-error="name">@error('name'){{ $message }}@enderror</span>
                        </div>
                        <div class="scorecard-form-field" data-assessment-field="email">
                            <label for="assessment-email">Work email <span aria-hidden="true">*</span></label>
                            <input id="assessment-email" name="email" type="email" maxlength="254" autocomplete="email" required value="{{ old('email') }}" aria-describedby="assessment-email-error">
                            <span class="scorecard-field-error" id="assessment-email-error" data-assessment-error="email">@error('email'){{ $message }}@enderror</span>
                        </div>
                        <div class="scorecard-form-field" data-assessment-field="company">
                            <label for="assessment-company">Organisation <span class="scorecard-optional">Optional</span></label>
                            <input id="assessment-company" name="company" type="text" maxlength="180" autocomplete="organization" value="{{ old('company') }}" aria-describedby="assessment-company-error">
                            <span class="scorecard-field-error" id="assessment-company-error" data-assessment-error="company">@error('company'){{ $message }}@enderror</span>
                        </div>
                        <div class="scorecard-form-field" data-assessment-field="preferred_timing">
                            <label for="assessment-timing">When would you like to start? <span class="scorecard-optional">Optional</span></label>
                            <select id="assessment-timing" name="preferred_timing" aria-describedby="assessment-timing-error">
                                <option value="">No preference</option>
                                @foreach (['As soon as possible', 'Within the next two weeks', 'Within the next month', 'I am exploring options'] as $timing)
                                    <option value="{{ $timing }}" @selected(old('preferred_timing') === $timing)>{{ $timing }}</option>
                                @endforeach
                            </select>
                            <span class="scorecard-field-error" id="assessment-timing-error" data-assessment-error="preferred_timing">@error('preferred_timing'){{ $message }}@enderror</span>
                        </div>
                    </div>
                </fieldset>

                <div class="scorecard-wizard-feedback" role="status" aria-live="polite" data-assessment-feedback></div>
                <div class="scorecard-wizard-actions">
                    <button class="button button-outline" type="button" data-assessment-previous hidden>Back</button>
                    <span class="scorecard-wizard-spacer"></span>
                    <button class="button button-dark" type="button" data-assessment-next>Continue <span aria-hidden="true">→</span></button>
                    <button class="button button-gold" type="submit" data-assessment-submit hidden>Send assessment request <span aria-hidden="true">↗</span></button>
                </div>
            </form>
        </div>
    </section>
@endsection
