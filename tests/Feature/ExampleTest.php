<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_public_information_routes_are_available(): void
    {
        $this->seed();

        $this->get('/language/id');
        $this->get('/layanan')->assertOk()->assertSee('Temukan layanan');
        $this->get('/artikel')->assertOk()->assertSee('Artikel');
        $this->get('/faq')->assertOk()->assertSee('Pertanyaan');
        $this->get('/profil')->assertOk()->assertSee('Unit Layanan Terpadu')->assertSee('PASTI');
        $this->get('/pencarian?q=KTM')->assertOk()->assertSee('Penggantian KTM');
    }

    public function test_language_switcher_changes_the_public_interface(): void
    {
        $this->seed();

        $this->get('/language/en')->assertRedirect();
        $this->get('/')->assertOk()->assertSee('Find campus services')->assertSee('Accessibility Menu');
        $this->get('/language/id')->assertRedirect();
        $this->get('/')->assertOk()->assertSee('Temukan layanan kampus');
    }

    public function test_social_contacts_are_visible(): void
    {
        $this->seed();

        $this->get('/kontak')->assertOk()->assertSee('WhatsApp Admin ULT')->assertSee('Helpdesk Unpad')->assertSee('Instagram ULT Unpad')->assertSee('TikTok ULT Unpad');
    }

    public function test_new_discovery_interfaces_and_profile_scores_are_available(): void
    {
        $this->seed();
        $this->get('/language/id');

        $this->get('/')->assertOk()->assertSee('data-article-carousel', false);
        $this->get('/profil')->assertOk()
            ->assertSee('Skor pelayanan per triwulan')
            ->assertSee('data-satisfaction-source', false)
            ->assertDontSee('22.08', false);
        $this->get('/layanan?q=KTM')->assertOk()
            ->assertSee('Penggantian KTM')
            ->assertSee('layanan ditemukan');
        $this->get('/faq?category=Akademik')->assertOk()
            ->assertSee('Jelajahi berdasarkan topik');
    }
}
