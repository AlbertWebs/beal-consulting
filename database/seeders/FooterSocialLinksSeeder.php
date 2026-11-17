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
        $links = [
            ['label' => 'LinkedIn', 'url' => 'https://ke.linkedin.com/company/beal-consulting-ltdke', 'enabled' => true],
            ['label' => 'Facebook', 'url' => 'https://www.facebook.com/', 'enabled' => true],
        ];
        $setting = SiteSetting::query()->firstOrCreate(
            ['setting_key' => 'footer.social_links'],
            ['setting_group' => 'footer', 'value' => ['value' => $links]],
        );

        $currentLinks = $setting->value['value'] ?? $setting->value;
        $changed = false;

        foreach ($links as $defaultLink) {
            $matchIndex = collect($currentLinks)->search(fn (mixed $link): bool => is_array($link)
                && strcasecmp((string) ($link['label'] ?? ''), $defaultLink['label']) === 0);

            if ($matchIndex === false) {
                $currentLinks[] = $defaultLink;
                $changed = true;

                continue;
            }

            if (! filled($currentLinks[$matchIndex]['url'] ?? '') && filled($defaultLink['url'])) {
                $currentLinks[$matchIndex]['url'] = $defaultLink['url'];
                $changed = true;
            }
        }

        if ($changed) {
            $setting->update(['value' => ['value' => $currentLinks]]);
        }
    }
}
