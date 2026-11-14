<?php

namespace App\Services;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;

class WebsiteSettings
{
    /** @return array<string, mixed> */
    public function all(): array
    {
        return Cache::remember('cms.website-settings', 3600, function (): array {
            return SiteSetting::query()->with('media')->get()->mapWithKeys(function (SiteSetting $setting): array {
                $value = $setting->value['value'] ?? $setting->value;
                $mediaUrl = $setting->media?->url;

                return [$setting->setting_key => $mediaUrl ?? $value];
            })->all();
        });
    }

    /** @return array<string, mixed> */
    public function group(string $group): array
    {
        $prefix = $group.'.';

        return collect($this->all())->filter(fn (mixed $value, string $key): bool => str_starts_with($key, $prefix))
            ->mapWithKeys(fn (mixed $value, string $key): array => [substr($key, strlen($prefix)) => $value])
            ->all();
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->all()[$key] ?? $default;
    }

    public function clearCache(): void
    {
        Cache::forget('cms.website-settings');
    }
}
