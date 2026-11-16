<?php

namespace App\Http\Controllers;

use App\Mail\EnquiryReceived;
use App\Models\ContactSubmission;
use App\Models\WebsiteService;
use App\Services\WebsiteSettings;
use Illuminate\Http\JsonResponse;
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
            'phone' => ['required', 'string', 'max:40'],
            'company' => ['nullable', 'string', 'max:180'],
            'service' => ['nullable', 'string', 'max:160', Rule::in(WebsiteService::query()->where('status', 'published')->pluck('title')->all())],
            'message' => ['required', 'string', 'max:5000'],
            'website' => ['nullable', 'prohibited'],
        ]);

        ContactSubmission::query()->create($enquiry);
        $recipient = $settings->get('integrations.contact_recipient_email') ?: config('beal.contact_email');

        if (! is_string($recipient)
            || filter_var($recipient, FILTER_VALIDATE_EMAIL) === false
            || in_array(config('mail.default'), ['log', 'array'], true)) {
            return response()->json(['message' => 'Thank you. Your enquiry has been received and the BEAL team will follow up.']);
        }

        try {
            Mail::to($recipient)->send(new EnquiryReceived($enquiry));
        } catch (Throwable $exception) {
            report($exception);

            return response()->json(['message' => 'Thank you. Your enquiry has been received and the BEAL team will follow up.']);
        }

        return response()->json(['message' => 'Thank you. Your enquiry has been received and the BEAL team will follow up.']);
    }
}
