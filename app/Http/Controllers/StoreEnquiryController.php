<?php

namespace App\Http\Controllers;

use App\Mail\EnquiryReceived;
use App\Models\ContactSubmission;
use App\Models\WebsiteService;
use App\Services\WebsiteSettings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class StoreEnquiryController extends Controller
{
    public function __invoke(Request $request, WebsiteSettings $settings): JsonResponse
    {
        if ($request->filled('website')) {
            return response()->json(['message' => 'Thank you. Your enquiry has been received and the BEAL team will follow up.']);
        }

        $enquiry = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:254'],
            'company' => ['nullable', 'string', 'max:180'],
            'service' => ['nullable', 'string', 'max:160', Rule::in(WebsiteService::query()->where('status', 'published')->pluck('title')->all())],
            'message' => ['required', 'string', 'max:5000'],
            'website' => ['nullable', 'prohibited'],
        ]);

        $this->storeAndNotify($enquiry, $settings);

        return response()->json(['message' => 'Thank you. Your enquiry has been received and the BEAL team will follow up.']);
    }

    public function assessment(Request $request, WebsiteSettings $settings): JsonResponse|RedirectResponse
    {
        $successMessage = 'Your Project Health Assessment request has been received. The BEAL team will follow up to discuss next steps.';

        if ($request->filled('website')) {
            return $request->expectsJson()
                ? response()->json(['message' => $successMessage])
                : redirect()->route('scorecard.assessment')->with('success', $successMessage);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:254'],
            'company' => ['nullable', 'string', 'max:180'],
            'project_name' => ['required', 'string', 'max:180'],
            'project_type' => ['required', 'string', Rule::in(['Commercial property', 'Residential development', 'Infrastructure & transport', 'Hospitality', 'Institutional', 'Energy & utilities', 'Industrial & manufacturing', 'Other'])],
            'location' => ['required', 'string', 'max:180'],
            'project_stage' => ['required', 'string', Rule::in(['Feasibility & planning', 'Design & approvals', 'Procurement & mobilisation', 'Construction', 'Commissioning & handover', 'Operational or recovery'])],
            'budget_range' => ['nullable', 'string', Rule::in(['Below KES 50 million', 'KES 50–250 million', 'KES 250 million–1 billion', 'Above KES 1 billion'])],
            'cost_position' => ['required', 'string', Rule::in(['Tracking to plan', 'Forecast overrun or cost pressure', 'No reliable cost baseline', 'Unsure'])],
            'schedule_position' => ['required', 'string', Rule::in(['Tracking to plan', 'At risk of delay', 'Behind schedule', 'No reliable baseline', 'Unsure'])],
            'concerns' => ['required', 'array', 'min:1'],
            'concerns.*' => ['required', 'string', 'distinct', Rule::in(['Cost', 'Schedule', 'Quality', 'Procurement', 'Commercial', 'HSE', 'Governance', 'Delivery'])],
            'project_context' => ['required', 'string', 'max:2000'],
            'assessment_goal' => ['required', 'string', 'max:1500'],
            'preferred_timing' => ['nullable', 'string', Rule::in(['As soon as possible', 'Within the next two weeks', 'Within the next month', 'I am exploring options'])],
            'website' => ['nullable', 'prohibited'],
        ]);

        $message = implode("\n\n", [
            'PROJECT HEALTH ASSESSMENT REQUEST',
            'Project: '.$data['project_name'],
            'Project type: '.$data['project_type'],
            'Location: '.$data['location'],
            'Current stage: '.$data['project_stage'],
            'Approved budget range: '.($data['budget_range'] ?: 'Not provided'),
            'Cost position: '.$data['cost_position'],
            'Schedule position: '.$data['schedule_position'],
            'Assessment areas: '.implode(', ', $data['concerns']),
            'Project context: '.$data['project_context'],
            'What the assessment should clarify: '.$data['assessment_goal'],
            'Preferred timing: '.($data['preferred_timing'] ?: 'No preference'),
        ]);

        $this->storeAndNotify([
            'name' => $data['name'],
            'email' => $data['email'],
            'company' => $data['company'] ?? null,
            'service' => 'Governance, Risk & Performance',
            'message' => $message,
        ], $settings);

        return $request->expectsJson()
            ? response()->json(['message' => $successMessage])
            : redirect()->route('scorecard.assessment')->with('success', $successMessage);
    }

    public function partnership(Request $request, WebsiteSettings $settings): RedirectResponse
    {
        $successMessage = 'Thank you. Your partnership enquiry has been received. The BEAL team will be in touch.';

        if ($request->filled('website')) {
            return redirect()->route('pages.contact', ['enquiry' => 'professional-partner'])->with('success', $successMessage);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:254'],
            'position' => ['required', 'string', 'max:120'],
            'company' => ['required', 'string', 'max:180'],
            'phone' => ['nullable', 'string', 'max:50'],
            'home_country' => ['required', 'string', 'max:100'],
            'markets' => ['nullable', 'string', 'max:300'],
            'partner_type' => ['required', 'string', Rule::in([
                'International consultancy or engineering firm',
                'Kenyan or African professional firm',
                'Contractor or delivery partner',
                'Development institution or public agency',
                'Investor or property developer',
                'Specialist service provider',
                'Other',
            ])],
            'sectors' => ['required', 'array', 'min:1'],
            'sectors.*' => ['required', 'string', 'distinct', Rule::in([
                'Commercial property',
                'Infrastructure and transport',
                'Energy and utilities',
                'Public and institutional',
                'Hospitality and tourism',
                'Industrial and manufacturing',
                'Housing and urban development',
                'Other',
            ])],
            'capabilities' => ['required', 'string', 'max:1800'],
            'collaboration_interest' => ['required', 'string', Rule::in([
                'Work together on a live project or bid',
                'Build a local delivery partnership',
                'Provide specialist technical support',
                'Explore a longer-term framework',
                'Introduce our organisation and capabilities',
            ])],
            'opportunity_status' => ['required', 'string', Rule::in([
                'Active project or tender',
                'Planned project pipeline',
                'General partnership discussion',
            ])],
            'opportunity_details' => ['required', 'string', 'max:2000'],
            'preferred_follow_up' => ['required', 'string', Rule::in(['Email', 'Phone', 'WhatsApp'])],
            'challenge_token' => ['required', 'string', 'alpha_num', 'size:40'],
            'math_answer' => ['required', 'integer', 'min:0', 'max:21'],
        ]);

        $challenge = $request->session()->get('partner_math', []);
        $tokenMatches = is_array($challenge)
            && hash_equals((string) ($challenge['token'] ?? ''), $data['challenge_token']);
        $answerMatches = is_array($challenge)
            && isset($challenge['answer'])
            && (int) $challenge['answer'] === (int) $data['math_answer'];

        if (! $tokenMatches || ! $answerMatches) {
            throw ValidationException::withMessages([
                'math_answer' => 'That answer did not match. Please try the new question.',
            ]);
        }

        $request->session()->forget('partner_math');

        $message = implode("\n\n", [
            'PROFESSIONAL PARTNERSHIP ENQUIRY',
            'Position: '.$data['position'],
            'Phone: '.($data['phone'] ?: 'Not provided'),
            'Organisation type: '.$data['partner_type'],
            'Home country: '.$data['home_country'],
            'Markets of interest: '.($data['markets'] ?: 'Not provided'),
            'Sectors: '.implode(', ', $data['sectors']),
            'Capabilities and contribution: '.$data['capabilities'],
            'Partnership interest: '.$data['collaboration_interest'],
            'Opportunity status: '.$data['opportunity_status'],
            'Opportunity details: '.$data['opportunity_details'],
            'Preferred follow-up: '.$data['preferred_follow_up'],
        ]);

        $this->storeAndNotify([
            'name' => $data['name'],
            'email' => $data['email'],
            'company' => $data['company'],
            'service' => 'Professional Partnership',
            'message' => $message,
        ], $settings);

        return redirect()->route('pages.contact', ['enquiry' => 'professional-partner'])->with('success', $successMessage);
    }

    /** @param array{name: string, email: string, company: ?string, service: ?string, message: string} $enquiry */
    private function storeAndNotify(array $enquiry, WebsiteSettings $settings): void
    {
        ContactSubmission::query()->create($enquiry);
        $recipient = $settings->get('integrations.contact_recipient_email') ?: config('beal.contact_email');

        if (! is_string($recipient)
            || filter_var($recipient, FILTER_VALIDATE_EMAIL) === false
            || in_array(config('mail.default'), ['log', 'array'], true)) {
            return;
        }

        try {
            Mail::to($recipient)->send(new EnquiryReceived($enquiry));
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
