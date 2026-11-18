<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Services\CmsAuditLogger;
use App\Services\WebsiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function edit(?string $group = 'general'): View
    {
        $groups = config('cms.settings');
        abort_unless(array_key_exists($group, $groups), 404);
        $settings = app(WebsiteSettings::class);
        $values = $settings->group($group);

        if ($group === 'contact') {
            $legacyKeys = [
                'email' => 'general.public_email',
                'phone' => 'general.phone',
                'location' => 'general.address',
                'maps_url' => 'general.maps_url',
                'business_hours' => 'general.business_hours',
            ];

            foreach ($legacyKeys as $key => $legacyKey) {
                if (! array_key_exists($key, $values)) {
                    $values[$key] = $settings->get($legacyKey, $groups['contact'][$key]);
                }
            }
        }

        return view('admin.settings.edit', ['group' => $group, 'groups' => $groups, 'values' => $values]);
    }

    public function update(Request $request, string $group, WebsiteSettings $settings, CmsAuditLogger $audit): RedirectResponse
    {
        $schema = config('cms.settings.'.$group);
        abort_unless(is_array($schema), 404);
        $rules = [];

        foreach ($schema as $key => $value) {
            $rules['settings.'.$key] = ['nullable', 'string', 'max:12000'];
        }

        $data = $request->validate($rules)['settings'] ?? [];
        DB::transaction(function () use ($data, $schema, $group, $request): void {
            foreach ($schema as $key => $default) {
                $value = $data[$key] ?? (is_array($default) ? '[]' : '');

                if (is_array($default)) {
                    $value = json_decode((string) $value, true);
                    abort_unless(is_array($value), 422, 'Structured settings must contain valid JSON.');
                    $this->validateStructuredUrls($value);

                    if (in_array($key, ['navigation', 'social_links'], true)) {
                        abort_unless(array_is_list($value), 422, 'Menu and social link settings must be lists.');

                        foreach ($value as $item) {
                            abort_unless(is_array($item) && is_string($item['label'] ?? null) && is_string($item['url'] ?? null), 422, 'Each menu or social link needs a text label and URL.');
                            abort_unless(mb_strlen($item['label']) <= 100 && mb_strlen($item['url']) <= 2048, 422, 'Menu labels or URLs are too long.');
                            abort_unless(! isset($item['enabled']) || is_bool($item['enabled']), 422, 'The enabled value must be true or false.');
                        }
                    }
                }

                if (str_ends_with($key, '_url') && is_string($value) && $value !== '') {
                    abort_unless($this->isSafeUrl($value), 422, 'Website links must use a relative path or http(s) URL.');
                }

                if ($group === 'branding' && in_array($key, ['horizontal_logo', 'light_logo', 'dark_logo', 'favicon', 'social_sharing_image'], true) && $value !== '') {
                    abort_unless($this->isSafeUrl($value), 422, 'Brand asset paths must use a relative path or http(s) URL.');
                }

                if ($group === 'branding' && str_ends_with($key, '_color')) {
                    abort_unless(is_string($value) && preg_match('/^#[0-9a-fA-F]{6}$/', $value) === 1, 422, 'Brand colors must be six digit hexadecimal values.');
                }

                if (in_array($key, ['public_email', 'contact_recipient_email', 'email'], true) && $value !== '') {
                    validator(['email' => $value], ['email' => ['email', 'max:254']])->validate();
                }

                if ($group === 'contact' && $key === 'whatsapp' && $value !== '') {
                    $digits = preg_replace('/\D/', '', $value);
                    abort_unless(preg_match('/^\+?[0-9\s().-]+$/', $value) === 1 && strlen($digits) >= 8 && strlen($digits) <= 15, 422, 'Enter a WhatsApp number in international format.');
                }

                SiteSetting::query()->updateOrCreate(
                    ['setting_key' => $group.'.'.$key],
                    ['setting_group' => $group, 'value' => ['value' => $value], 'updated_by' => $request->user()->id],
                );
            }
        });
        $settings->clearCache();
        $audit->record('settings.updated', 'Updated '.$group.' website settings');

        return back()->with('success', ucfirst($group).' settings saved.');
    }

    /** @param array<string|int, mixed> $values */
    private function validateStructuredUrls(array $values): void
    {
        foreach ($values as $key => $value) {
            if (is_array($value)) {
                $this->validateStructuredUrls($value);
            } elseif ($key === 'url' && is_string($value)) {
                abort_unless($this->isSafeUrl($value), 422, 'Website links must use a relative path or http(s) URL.');
            }
        }
    }

    private function isSafeUrl(string $url): bool
    {
        return $url === '' || str_starts_with($url, '#') || str_starts_with($url, '/') && ! str_starts_with($url, '//') || filter_var($url, FILTER_VALIDATE_URL) !== false && in_array(parse_url($url, PHP_URL_SCHEME), ['http', 'https'], true);
    }
}
