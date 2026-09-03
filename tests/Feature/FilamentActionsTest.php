<?php

namespace Tests\Feature;

use App\Filament\Resources\ArticleResource\Pages\ManageArticles;
use App\Filament\Resources\ContactResource\Pages\ManageContacts;
use App\Filament\Resources\FaqResource\Pages\ManageFaqs;
use App\Filament\Resources\QuickLinkResource\Pages\ManageQuickLinks;
use App\Filament\Resources\ServiceCategoryResource\Pages\ManageServiceCategories;
use App\Filament\Resources\ServiceResource\Pages\ManageServices;
use App\Models\Article;
use App\Models\Contact;
use App\Models\Faq;
use App\Models\QuickLink;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FilamentActionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        $this->actingAs(User::factory()->create(['email' => 'crud@unpad.ac.id', 'email_verified_at' => now(), 'is_admin' => true]));
    }

    public function test_contact_crud_actions_work(): void
    {
        Livewire::test(ManageContacts::class)->callAction('create', data: ['label' => 'Telepon Tes', 'label_en' => 'Test Phone', 'type' => 'phone', 'value' => '123', 'url' => 'https://example.com', 'is_published' => true]);
        $record = Contact::where('label', 'Telepon Tes')->firstOrFail();
        Livewire::test(ManageContacts::class)->callTableAction('edit', $record, data: ['label' => 'Telepon Diedit', 'type' => 'phone', 'value' => '456', 'is_published' => true]);
        $this->assertDatabaseHas('contacts', ['id' => $record->id, 'label' => 'Telepon Diedit']);
        Livewire::test(ManageContacts::class)->callTableAction('delete', $record->fresh());
        $this->assertDatabaseMissing('contacts', ['id' => $record->id]);
    }

    public function test_category_and_service_crud_actions_work(): void
    {
        Livewire::test(ManageServiceCategories::class)->callAction('create', data: ['name' => 'Kategori Tes', 'name_en' => 'Test Category', 'slug' => 'kategori-tes', 'sort_order' => 1]);
        $category = ServiceCategory::where('slug', 'kategori-tes')->firstOrFail();
        Livewire::test(ManageServices::class)->callAction('create', data: ['service_category_id' => $category->id, 'slug' => 'layanan-tes', 'delivery_type' => 'online', 'title' => 'Layanan Tes', 'title_en' => 'Test Service', 'summary' => 'Ringkasan', 'summary_en' => 'Summary', 'cta_label' => 'Buka', 'is_published' => true]);
        $service = Service::where('slug', 'layanan-tes')->firstOrFail();
        Livewire::test(ManageServices::class)->callTableAction('edit', $service, data: ['service_category_id' => $category->id, 'slug' => 'layanan-tes', 'delivery_type' => 'hybrid', 'title' => 'Layanan Diedit', 'summary' => 'Ringkasan', 'cta_label' => 'Buka', 'is_published' => true]);
        $this->assertDatabaseHas('services', ['id' => $service->id, 'title' => 'Layanan Diedit', 'delivery_type' => 'hybrid']);
        Livewire::test(ManageServices::class)->callTableAction('delete', $service->fresh());
        Livewire::test(ManageServiceCategories::class)->callTableAction('delete', $category->fresh());
        $this->assertDatabaseMissing('service_categories', ['id' => $category->id]);
    }

    public function test_article_faq_and_quick_link_crud_actions_work(): void
    {
        Livewire::test(ManageArticles::class)->callAction('create', data: ['title' => 'Artikel Tes', 'slug' => 'artikel-tes', 'category' => 'Tes', 'excerpt' => 'Ringkasan', 'content' => '<p>Isi</p>', 'author' => 'ULT', 'is_published' => true]);
        $article = Article::where('slug', 'artikel-tes')->firstOrFail();
        Livewire::test(ManageArticles::class)->callTableAction('edit', $article, data: ['title' => 'Artikel Diedit', 'slug' => 'artikel-tes', 'category' => 'Tes', 'excerpt' => 'Ringkasan', 'content' => '<p>Isi</p>', 'author' => 'ULT', 'is_published' => true]);
        Livewire::test(ManageArticles::class)->callTableAction('delete', $article->fresh());

        Livewire::test(ManageFaqs::class)->callAction('create', data: ['question' => 'Pertanyaan tes?', 'answer' => '<p>Jawaban</p>', 'category' => 'Tes', 'is_published' => true]);
        $faq = Faq::where('question', 'Pertanyaan tes?')->firstOrFail();
        Livewire::test(ManageFaqs::class)->callTableAction('edit', $faq, data: ['question' => 'Pertanyaan diedit?', 'answer' => '<p>Jawaban</p>', 'category' => 'Tes', 'is_published' => true]);
        Livewire::test(ManageFaqs::class)->callTableAction('delete', $faq->fresh());

        Livewire::test(ManageQuickLinks::class)->callAction('create', data: ['name' => 'Tautan Tes', 'url' => 'https://example.com', 'is_published' => true]);
        $link = QuickLink::where('name', 'Tautan Tes')->firstOrFail();
        Livewire::test(ManageQuickLinks::class)->callTableAction('edit', $link, data: ['name' => 'Tautan Diedit', 'url' => 'https://example.com/edited', 'is_published' => true]);
        Livewire::test(ManageQuickLinks::class)->callTableAction('delete', $link->fresh());

        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
        $this->assertDatabaseMissing('faqs', ['id' => $faq->id]);
        $this->assertDatabaseMissing('quick_links', ['id' => $link->id]);
    }
}
