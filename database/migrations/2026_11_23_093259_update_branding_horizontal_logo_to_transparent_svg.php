<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->replaceLogo('/assets/beal-horizontal-logo.svg', '/assets/beal-horizontal-logo-transparent.svg');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->replaceLogo('/assets/beal-horizontal-logo-transparent.svg', '/assets/beal-horizontal-logo.svg');
    }

    private function replaceLogo(string $currentUrl, string $replacementUrl): void
    {
        $setting = DB::table('site_settings')
            ->where('setting_group', 'branding')
            ->where('setting_key', 'branding.horizontal_logo')
            ->first(['id', 'value', 'media_asset_id']);

        if ($setting === null || $setting->media_asset_id !== null) {
            return;
        }

        $value = json_decode((string) $setting->value, true);

        if (! is_array($value) || ($value['value'] ?? null) !== $currentUrl) {
            return;
        }

        $value['value'] = $replacementUrl;

        DB::table('site_settings')->where('id', $setting->id)->update([
            'value' => json_encode($value, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ]);
    }
};
