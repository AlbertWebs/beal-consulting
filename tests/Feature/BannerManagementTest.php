<?php

namespace Tests\Feature;

use App\Models\MediaAsset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BannerManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_editor_can_assign_a_banner_image_and_the_homepage_uses_it(): void
    {
        $editor = User::factory()->create(['role' => 'editor', 'is_active' => true]);
        $asset = MediaAsset::query()->create([
            'disk' => 'public',
            'path' => 'cms-media/home-banner.jpg',
            'original_name' => 'home-banner.jpg',
            'mime_type' => 'image/jpeg',
            'file_size' => 2048,
            'width' => 1600,
            'height' => 900,
            'alt_text' => 'Nairobi skyline',
            'uploaded_by' => $editor->id,
        ]);

        $this->actingAs($editor)
            ->get(route('admin.banners.edit'))
            ->assertOk()
            ->assertSee('Website banners');

        $this->actingAs($editor)
            ->put(route('admin.banners.update'), ['banners' => ['home_hero' => $asset->id]])
            ->assertRedirect(route('admin.banners.edit'));

        $this->assertDatabaseHas('site_settings', [
            'setting_key' => 'banners.home_hero',
            'media_asset_id' => $asset->id,
        ]);

        $this->get(route('home'))->assertOk()->assertSee('/storage/cms-media/home-banner.jpg');
    }

    public function test_non_image_media_cannot_be_assigned_to_a_banner(): void
    {
        $editor = User::factory()->create(['role' => 'editor', 'is_active' => true]);
        $asset = MediaAsset::query()->create([
            'disk' => 'public',
            'path' => 'cms-media/report.pdf',
            'original_name' => 'report.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 2048,
            'uploaded_by' => $editor->id,
        ]);

        $this->actingAs($editor)
            ->put(route('admin.banners.update'), ['banners' => ['home_hero' => $asset->id]])
            ->assertUnprocessable();

        $this->assertDatabaseMissing('site_settings', ['setting_key' => 'banners.home_hero']);
    }
}
