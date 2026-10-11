<?php

namespace Tests\Feature;

use App\Models\Faq;
use Database\Seeders\FaqCatalogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FaqCatalogSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_replaces_old_faqs_with_the_bilingual_workbook_catalog(): void
    {
        Faq::create([
            'question' => 'FAQ lama',
            'answer' => 'Harus dihapus',
            'category' => 'Lama',
        ]);

        $this->seed(FaqCatalogSeeder::class);

        $this->assertDatabaseMissing('faqs', ['question' => 'FAQ lama']);
        $this->assertSame(56, Faq::count());
        $this->assertSame(56, Faq::where('is_published', true)->count());
        $this->assertSame(0, Faq::whereNull('question_en')->orWhereNull('answer_en')->orWhereNull('category_en')->count());
        $this->assertSame([
            'Biaya' => 18,
            'Informasi Umum Unit Layanan Terpadu (ULT) Unpad' => 7,
            'Lulusan' => 10,
            'Mahasiswa Aktif' => 8,
            'Mahasiswa Baru' => 13,
        ], Faq::query()->selectRaw('category, count(*) as total')->groupBy('category')->pluck('total', 'category')->sortKeys()->all());
    }

    public function test_faq_page_lists_categories_and_english_content(): void
    {
        $this->seed(FaqCatalogSeeder::class);

        $this->get('/language/id');
        $this->get('/faq')->assertOk()
            ->assertSee('Pertanyaan berdasarkan kebutuhan Anda')
            ->assertSee('Mahasiswa Baru')
            ->assertSee('56 jawaban tersedia');
        $this->get('/faq?category=Mahasiswa+Baru')->assertOk()
            ->assertSee('Apa sanksinya apabila tidak melakukan registrasi mahasiswa baru sesuai jadwal dan ketentuan?')
            ->assertDontSee('Bagaimana cara menemukan layanan yang tepat?');

        $this->get('/language/en');
        $this->get('/faq')->assertOk()
            ->assertSee('Questions organized around your needs');
        $this->get('/faq?category=New+Students')->assertOk()
            ->assertSee('What happens if I do not complete new student registration according to the schedule and requirements?');
    }
}
