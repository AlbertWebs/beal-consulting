<?php

namespace Tests\Feature;

use App\Models\ContentEntry;
use App\Models\MediaAsset;
use App\Models\PageSection;
use App\Models\User;
use App\Models\WebsitePage;
use App\Models\WebsiteService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CmsFeatureTest extends TestCase
{
    public function test_public_enquiry_is_saved_even_when_outbound_mail_is_unconfigured(): void
    {
        $response = $this->postJson(route('enquiries.store'), [
            'name' => 'Amina Otieno',
            'email' => 'amina@example.com',
            'company' => 'Build Co',
            'service' => WebsiteService::query()->firstOrFail()->title,
            'message' => 'Please contact me about a project.',
        ]);

        $response->assertOk()->assertJsonPath('message', 'Thank you. Your enquiry has been received and the BEAL team will follow up.');
        $this->assertDatabaseHas('contact_submissions', ['email' => 'amina@example.com', 'status' => 'new']);
    }

    public function test_cms_requires_authentication_and_respects_module_permissions(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));

        $editor = User::factory()->create(['role' => 'editor', 'is_active' => true]);
        $this->actingAs($editor)->get(route('admin.dashboard'))->assertOk();
        $this->get(route('admin.submissions.index'))->assertForbidden();
        auth()->logout();
        $this->get(route('home', ['cms_preview' => 1]))->assertNotFound();
    }

    public function test_admin_can_save_sanitized_homepage_copy_and_it_renders_publicly(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin', 'is_active' => true]);
        $page = WebsitePage::query()->where('page_key', 'home')->firstOrFail();
        $section = PageSection::query()->where('website_page_id', $page->id)->where('section_key', 'hero')->firstOrFail();

        $this->actingAs($admin)->put(route('admin.pages.sections.update', [$page, $section]), [
            'is_enabled' => '1',
            'content' => ['summary' => '<p>Updated BEAL introduction.</p><script>alert(1)</script>'],
        ])->assertRedirect();

        $this->assertDatabaseHas('cms_revisions', ['revisable_id' => $section->id]);
        $this->get(route('home'))->assertOk()->assertDontSee('Updated BEAL introduction.');
        $this->get(route('home', ['cms_preview' => 1]))->assertOk()->assertSee('Updated BEAL introduction.')->assertDontSee('alert(1)')->assertSee('noindex,nofollow');
        $this->post(route('admin.pages.publish', $page))->assertRedirect();
        $this->get(route('home'))->assertOk()->assertSee('Updated BEAL introduction.')->assertDontSee('alert(1)');
    }

    public function test_admin_can_update_service_content_and_public_service_uses_the_database(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin', 'is_active' => true]);
        $service = WebsiteService::query()->firstOrFail();

        $this->actingAs($admin)->put(route('admin.services.update', $service), [
            'title' => 'Updated Advisory Service',
            'slug' => $service->slug,
            'number' => $service->number,
            'description' => 'A revised service description.',
            'description_html' => '<p>A revised <strong>service</strong> description.</p>',
            'image_path' => $service->image_path,
            'media_asset_id' => $service->media_asset_id,
            'image_alt' => 'Updated alt text',
            'subservices_text' => "Analysis\nPlanning",
            'status' => 'published',
            'seo_title' => 'Updated service SEO',
            'seo_description' => 'Updated service description for search.',
            'content_json' => json_encode(config('cms.service_page_defaults')),
        ])->assertRedirect();

        $this->get(route('services.show', ['service' => $service->slug]))->assertOk()->assertDontSee('Updated Advisory Service');
        $this->get(route('services.show', ['service' => $service->slug, 'cms_preview' => 1]))->assertOk()->assertSee('Updated Advisory Service');
        $this->post(route('admin.services.publish', $service))->assertRedirect();
        $this->get(route('services.show', ['service' => $service->slug]))->assertOk()->assertSee('Updated Advisory Service');
    }

    public function test_media_upload_records_dimensions_and_alternative_text(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'super_admin', 'is_active' => true]);

        $this->actingAs($admin)->post(route('admin.media.store'), [
            'alt_text' => 'A city building',
            'files' => [UploadedFile::fake()->image('building.png', 120, 80)],
        ])->assertRedirect();

        $this->assertDatabaseHas('media_assets', ['original_name' => 'building.png', 'alt_text' => 'A city building', 'width' => 120, 'height' => 80]);
        $asset = MediaAsset::query()->where('original_name', 'building.png')->firstOrFail();
        PageSection::query()->firstOrFail()->update(['draft_media_asset_id' => $asset->id]);
        $this->actingAs($admin)->delete(route('admin.media.destroy', $asset))->assertUnprocessable();
    }

    public function test_new_services_start_as_drafts_and_can_only_be_seen_after_publishing(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin', 'is_active' => true]);

        $this->actingAs($admin)->post(route('admin.services.store'), [
            'title' => 'New Infrastructure Advisory',
            'slug' => 'new-infrastructure-advisory',
            'number' => '06',
            'description' => 'A new service description.',
            'description_html' => '<p>A new service description.</p>',
            'image_path' => 'assets/home-image/glass-architecture.jpg',
            'image_alt' => 'Infrastructure advisory',
            'subservices_text' => "Planning\nDelivery",
            'status' => 'published',
            'content_json' => json_encode(config('cms.service_page_defaults')),
        ])->assertRedirect();

        $service = WebsiteService::query()->where('slug', 'new-infrastructure-advisory')->firstOrFail();
        $this->assertSame('draft', $service->status);
        $this->get(route('services.show', ['service' => $service->slug]))->assertNotFound();
        $this->get(route('services.show', ['service' => $service->slug, 'cms_preview' => 1]))->assertOk();
        $this->post(route('admin.services.publish', $service))->assertRedirect();
        $this->get(route('services.show', ['service' => $service->slug]))->assertOk()->assertSee('New Infrastructure Advisory');
    }

    public function test_settings_are_restricted_and_persist_when_saved_by_a_super_admin(): void
    {
        $administrator = User::factory()->create(['role' => 'administrator', 'is_active' => true]);
        $this->actingAs($administrator)->get(route('admin.users.index'))->assertForbidden();

        $superAdmin = User::factory()->create(['role' => 'super_admin', 'is_active' => true]);
        $this->actingAs($superAdmin)->put(route('admin.settings.update', ['group' => 'general']), [
            'settings' => ['site_name' => 'BEAL Test', 'tagline' => 'Test tagline', 'legal_name' => 'BEAL Consulting Ltd', 'site_url' => 'https://example.com', 'description' => 'Description', 'public_email' => 'hello@example.com', 'phone' => '', 'address' => 'Nairobi', 'maps_url' => '', 'business_hours' => '', 'timezone' => 'Africa/Nairobi', 'language' => 'en', 'copyright' => '© :year BEAL'],
        ])->assertRedirect();

        $this->get(route('home'))->assertOk()->assertSee('BEAL Test');
    }

    public function test_all_brief_pages_and_sector_detail_pages_render_from_published_content(): void
    {
        foreach (['about', 'services', 'sectors', 'insights', 'experience', 'contact'] as $page) {
            $this->get(route('pages.'.$page))->assertOk();
        }

        $this->get(route('pages.about'))->assertSee('intelligence-led consultancy');
        $this->get(route('pages.services'))->assertSee('Strategic Intelligence &amp; Advisory', false)->assertSee('Explore service');
        $this->get(route('pages.sectors'))->assertSee('Real estate & property development')->assertSee('Public sector & development programmes');
        $this->get(route('sectors.show', 'infrastructure-transport'))->assertOk()->assertSee('Infrastructure & transport')->assertSee('Strategic Intelligence & Advisory');
        $this->get(route('pages.insights'))->assertSee('No BEAL publications have been approved for release yet.');
        $this->get(route('pages.experience'))->assertSee('No project or experience records have been approved for publication yet.');
        $this->get(route('pages.contact'))->assertSee('Nairobi, Kenya')->assertSee('data-enquiry-open');
    }

    public function test_admin_can_create_sanitized_insight_draft_and_publish_it(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin', 'is_active' => true]);

        $this->actingAs($admin)->post(route('admin.content.store', ['type' => 'insight']), [
            'title' => 'Reading project signals',
            'slug' => 'reading-project-signals',
            'summary' => 'A short editorial perspective.',
            'content_html' => '<p>Look beyond the immediate metric.</p><script>alert(1)</script>',
            'category' => 'Project delivery',
            'author' => 'BEAL Consulting',
            'published_at' => now()->subDay()->format('Y-m-d\TH:i'),
            'is_featured' => '1',
            'seo_title' => 'Reading project signals | BEAL Consulting',
            'seo_description' => 'A short editorial perspective on project delivery signals.',
        ])->assertRedirect();

        $entry = ContentEntry::query()->where('slug', 'reading-project-signals')->firstOrFail();
        $this->assertSame('draft', $entry->status);
        $this->assertStringNotContainsString('<script', $entry->draft_data['content_html']);
        $this->get(route('insights.show', $entry->slug))->assertNotFound();
        $this->get(route('insights.show', ['slug' => $entry->slug, 'cms_preview' => 1]))->assertOk()->assertDontSee('alert(1)')->assertSee('noindex,nofollow');

        $this->actingAs($admin)->post(route('admin.content.publish', ['type' => 'insight', 'contentEntry' => $entry]))->assertRedirect();
        $this->assertDatabaseHas('content_entries', ['id' => $entry->id, 'status' => 'published', 'is_featured' => true]);
        $this->get(route('pages.insights', ['q' => 'project signals']))->assertOk()->assertSee('Reading project signals');
        $this->get(route('insights.show', $entry->slug))->assertOk()->assertSee('Look beyond the immediate metric.');
    }

    public function test_internal_page_sections_use_draft_and_publish_workflow(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin', 'is_active' => true]);
        $page = WebsitePage::query()->where('page_key', 'about')->firstOrFail();
        $section = PageSection::query()->where('website_page_id', $page->id)->where('section_key', 'about_overview')->firstOrFail();

        $this->actingAs($admin)->put(route('admin.pages.sections.update', [$page, $section]), [
            'is_enabled' => '1',
            'content' => ['overview' => '<p>Updated public profile.</p>'],
        ])->assertRedirect();

        $this->get(route('pages.about'))->assertDontSee('Updated public profile.');
        $this->get(route('pages.about', ['cms_preview' => 1]))->assertOk()->assertSee('Updated public profile.');
        $this->post(route('admin.pages.publish', $page))->assertRedirect();
        $this->get(route('pages.about'))->assertSee('Updated public profile.');
    }
}
