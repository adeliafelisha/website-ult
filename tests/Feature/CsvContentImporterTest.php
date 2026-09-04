<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Faq;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Support\CsvContentImporter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class CsvContentImporterTest extends TestCase
{
    use RefreshDatabase;

    public function test_articles_faqs_and_services_can_be_imported_from_csv(): void
    {
        ServiceCategory::firstOrCreate(['slug' => 'teknologi-informasi'], [
            'name' => 'Teknologi Informasi',
        ]);

        CsvContentImporter::articles(UploadedFile::fake()->createWithContent(
            'articles.csv',
            "title,slug,category,excerpt,content,external_url,is_published\nArtikel CSV,artikel-csv,Informasi,Ringkasan,<p>Isi</p>,https://example.com/artikel,1\n"
        ));
        CsvContentImporter::faqs(UploadedFile::fake()->createWithContent(
            'faqs.csv',
            "question,answer,category,external_url,is_published\nPertanyaan CSV?,Jawaban CSV,Umum,https://example.com/faq,1\n"
        ));
        CsvContentImporter::services(UploadedFile::fake()->createWithContent(
            'services.csv',
            "category_slug,title,slug,summary,delivery_type,contact_1_label,contact_1_channel,contact_1_url,is_published\nteknologi-informasi,Layanan CSV,layanan-csv,Ringkasan,online,Hubungi TI,helpdesk,https://example.com/layanan,1\n"
        ));

        $this->assertSame('https://example.com/artikel', Article::where('slug', 'artikel-csv')->firstOrFail()->external_url);
        $this->assertSame('https://example.com/faq', Faq::where('question', 'Pertanyaan CSV?')->firstOrFail()->external_url);
        $this->assertSame('https://example.com/layanan', Service::where('slug', 'layanan-csv')->firstOrFail()->contact_buttons[0]['url']);
    }
}
