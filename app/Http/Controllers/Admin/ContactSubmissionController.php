<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use App\Services\CmsAuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ContactSubmissionController extends Controller
{
    public function index(Request $request): View
    {
        $submissions = ContactSubmission::query()->when($request->string('status')->isNotEmpty(), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->string('q')->isNotEmpty(), fn ($query) => $query->where(fn ($builder) => $builder->where('name', 'like', '%'.$request->string('q').'%')->orWhere('email', 'like', '%'.$request->string('q').'%')))
            ->latest()->paginate(30)->withQueryString();

        return view('admin.submissions.index', ['submissions' => $submissions]);
    }

    public function show(ContactSubmission $contactSubmission, CmsAuditLogger $audit): View
    {
        if ($contactSubmission->read_at === null) {
            $contactSubmission->update(['read_at' => now(), 'status' => $contactSubmission->status === 'new' ? 'read' : $contactSubmission->status]);
            $audit->record('submission.read', 'Read enquiry from '.$contactSubmission->name, $contactSubmission);
        }

        return view('admin.submissions.show', ['submission' => $contactSubmission]);
    }

    public function update(Request $request, ContactSubmission $contactSubmission, CmsAuditLogger $audit): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', 'in:new,read,in_progress,handled,spam']]);
        $data['handled_at'] = $data['status'] === 'handled' ? now() : null;
        $contactSubmission->update($data);
        $audit->record('submission.updated', 'Updated enquiry status', $contactSubmission, $data);

        return back()->with('success', 'Enquiry status updated.');
    }

    public function export(): Response
    {
        $rows = ContactSubmission::query()->latest()->get(['name', 'email', 'phone', 'company', 'service', 'message', 'status', 'created_at']);
        $stream = fopen('php://temp', 'r+');
        fputcsv($stream, ['Name', 'Email', 'Telephone', 'Company', 'Service', 'Message', 'Status', 'Received']);

        foreach ($rows as $row) {
            fputcsv($stream, array_map(fn (?string $value): ?string => $value !== null && preg_match('/^\s*[=+@-]/', $value) === 1 ? "'".$value : $value, [$row->name, $row->email, $row->phone, $row->company, $row->service, $row->message, $row->status, $row->created_at->toIso8601String()]));
        }

        rewind($stream);
        $csv = stream_get_contents($stream);
        fclose($stream);

        return response($csv, 200, ['Content-Type' => 'text/csv; charset=UTF-8', 'Content-Disposition' => 'attachment; filename="beal-enquiries.csv"']);
    }
}
