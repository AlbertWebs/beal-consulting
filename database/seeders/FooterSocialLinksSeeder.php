<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class FooterSocialLinksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $links = config('cms.settings.footer.social_links', []);
        $setting = SiteSetting::query()->firstOrCreate(
            ['setting_key' => 'footer.social_links'],
            ['setting_group' => 'footer', 'value' => ['value' => $links]],
        );

        if (($setting->value['value'] ?? $setting->value) === []) {
            $setting->update(['value' => ['value' => $links]]);
        }
    }
}
