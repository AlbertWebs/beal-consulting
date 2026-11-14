<?php

namespace App\Services;

use App\Models\AdminActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class CmsAuditLogger
{
    public function __construct(private readonly Request $request) {}

    /** @param array<string, mixed> $properties */
    public function record(string $action, string $summary, ?Model $subject = null, array $properties = []): void
    {
        AdminActivity::query()->create([
            'user_id' => $this->request->user()?->id,
            'action' => $action,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'summary' => $summary,
            'properties' => $properties,
            'ip_address' => $this->request->ip(),
        ]);
    }
}
