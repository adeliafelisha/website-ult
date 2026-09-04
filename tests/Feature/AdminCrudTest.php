<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_verified_admin_can_open_all_crud_pages(): void
    {
        $this->seed();
        $user = User::factory()->create(['email' => 'operator@unpad.ac.id', 'email_verified_at' => now(), 'is_admin' => true]);
        foreach (['/admin', '/admin/services', '/admin/service-categories', '/admin/articles', '/admin/faqs', '/admin/contacts', '/admin/quick-links', '/admin/satisfaction-surveys'] as $url) {
            $this->actingAs($user)->get($url)->assertOk();
        }
    }

    public function test_cms_changes_are_immediately_visible_in_both_languages(): void
    {
        $category = ServiceCategory::create(['name' => 'Tes', 'name_en' => 'Test', 'slug' => 'tes', 'description' => 'Deskripsi', 'description_en' => 'Description']);
        $service = Service::create(['service_category_id' => $category->id, 'title' => 'Layanan Baru', 'title_en' => 'New Service', 'slug' => 'layanan-baru', 'summary' => 'Ringkasan baru', 'summary_en' => 'New summary', 'delivery_type' => 'online', 'cta_label' => 'Buka', 'cta_label_en' => 'Open', 'contact_buttons' => [['label' => 'WhatsApp ULT', 'label_en' => 'ULT WhatsApp', 'channel' => 'whatsapp', 'url' => 'https://wa.me/628123456789']], 'is_published' => true, 'published_at' => now()]);
        $this->get('/language/id');
        $this->get('/layanan/'.$service->slug)->assertSee('Layanan Baru');
        $this->get('/language/en');
        $this->get('/layanan/'.$service->slug)->assertSee('New Service')->assertSee('New summary')->assertSee('ULT WhatsApp');
        $service->update(['title_en' => 'Updated Service']);
        $this->get('/layanan/'.$service->slug)->assertSee('Updated Service');
        $service->delete();
        $this->get('/layanan/'.$service->slug)->assertNotFound();
    }
}
