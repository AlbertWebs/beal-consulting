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
