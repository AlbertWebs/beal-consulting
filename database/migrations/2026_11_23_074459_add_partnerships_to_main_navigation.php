<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $setting = DB::table('site_settings')->where('setting_key', 'header.navigation')->first(['id', 'value']);

        if ($setting === null) {
            DB::table('site_settings')->insert([
                'setting_group' => 'header',
                'setting_key' => 'header.navigation',
                'value' => json_encode(['value' => config('cms.settings.header.navigation', [])], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $setting = DB::table('site_settings')->where('setting_key', 'header.navigation')->first(['id', 'value']);
        }

        $storedValue = json_decode((string) $setting->value, true);
        $navigation = $storedValue['value'] ?? $storedValue;

        if (! is_array($navigation)) {
            return;
        }

        $partnershipsLinkExists = collect($navigation)->contains(fn (mixed $item): bool => is_array($item) && strtolower((string) ($item['label'] ?? '')) === 'partnerships');

        if (! $partnershipsLinkExists) {
            $navigation[] = ['label' => 'Partnerships', 'url' => '/partnerships', 'enabled' => true];
            DB::table('site_settings')->where('id', $setting->id)->update([
                'value' => json_encode(['value' => array_values($navigation)], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'updated_at' => now(),
            ]);
        }

        Cache::forget('cms.website-settings');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep the Partnerships link so rolling back does not remove a live menu item.
    }
};
