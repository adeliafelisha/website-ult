<?php

namespace Tests\Feature;

use App\Models\SatisfactionSurvey;
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
            ->assertSee('data-satisfaction-year', false)
            ->assertSee('Kuesioner survei kepuasan masyarakat')
            ->assertDontSee('22.08', false);
        $this->get('/layanan?q=KTM')->assertOk()
            ->assertSee('Penggantian KTM')
            ->assertSee('layanan ditemukan');
        $this->get('/faq?category=Akademik')->assertOk()
            ->assertSee('Jelajahi berdasarkan topik');
    }

    public function test_published_satisfaction_data_and_links_are_visible_on_profile(): void
    {
        SatisfactionSurvey::create([
            'year' => 2026,
            'quarter_1_score' => 91.25,
            'quarter_2_score' => 92.50,
            'quarter_3_score' => 93.75,
            'quarter_4_score' => 94.00,
            'source_url' => 'https://example.com/data-skm',
            'questionnaire_url' => 'https://example.com/kuesioner-skm',
            'is_published' => true,
        ]);

        $this->get('/language/id');
        $this->get('/profil')->assertOk()
            ->assertSee('91.25')
            ->assertSee('https://example.com/data-skm', false)
            ->assertSee('https://example.com/kuesioner-skm', false);
    }

    public function test_service_categories_have_their_own_directory_pages(): void
    {
        $this->seed();
        $this->get('/language/id');

        $this->get('/layanan')->assertOk()
            ->assertSee('Mahasiswa, Calon Mahasiswa, dan Alumni')
            ->assertSee('Dosen dan Tenaga Kependidikan')
            ->assertSee('Teknologi Informasi')
            ->assertSee('Internasional')
            ->assertSee('Sarana dan Prasarana')
            ->assertDontSee('name="category"', false)
            ->assertDontSee('name="audience"', false)
            ->assertDontSee('name="delivery"', false);

        $this->get('/layanan/kategori/mahasiswa-calon-mahasiswa-alumni')->assertOk()
            ->assertSee('Penggantian KTM Hilang atau Rusak')
            ->assertSee('Informasi UKT dan Registrasi')
            ->assertSee('Registrasi Mahasiswa Baru')
            ->assertSee('Bantuan Akun PAuS')
            ->assertSee('/layanan/penggantian-ktm', false);

        $this->get('/layanan/penggantian-ktm')->assertOk()
            ->assertSee('Kategori dan layanan')
            ->assertSee('Bantuan Akun PAuS')
            ->assertSee('Hubungi Kami')
            ->assertSee('Hubungi Admin ULT')
            ->assertSee('service-side', false)
            ->assertDontSee('Langkah selanjutnya')
            ->assertSee('aria-current="page"', false);
    }
}
