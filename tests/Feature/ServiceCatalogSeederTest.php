<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\ServiceCategory;
use Database\Seeders\ContactSeeder;
use Database\Seeders\ServiceCatalogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceCatalogSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_import_creates_editable_published_services_with_images(): void
    {
        $this->seed(ContactSeeder::class);
        $this->seed(ServiceCatalogSeeder::class);

        $this->assertSame(8, ServiceCategory::where('slug', 'calon-mahasiswa')->firstOrFail()->services()->count());
        $this->assertSame(7, ServiceCategory::where('slug', 'mahasiswa')->firstOrFail()->services()->count());
        $this->assertSame(3, ServiceCategory::where('slug', 'alumni')->firstOrFail()->services()->count());
        $this->assertSame(2, ServiceCategory::where('slug', 'layanan-disabilitas')->firstOrFail()->services()->count());
        $this->assertSame(4, ServiceCategory::count());
        $service = Service::where('slug', 'calon-mahasiswa-verifikasi-akun-paus')->firstOrFail();
        $this->assertTrue($service->is_published);
        $this->assertStringContainsString('<img src="/images/service-guides/', $service->procedure);
        $this->assertNotEmpty($service->contact_buttons);

        $this->get('/layanan/'.$service->slug)
            ->assertOk()
            ->assertSee('/images/service-guides/calon-mahasiswa/', false);
    }

    public function test_catalog_import_is_idempotent(): void
    {
        $this->seed(ContactSeeder::class);
        $this->seed(ServiceCatalogSeeder::class);
        $this->seed(ServiceCatalogSeeder::class);

        $categoryIds = ServiceCategory::whereIn('slug', ['calon-mahasiswa', 'mahasiswa', 'alumni', 'layanan-disabilitas'])->pluck('id');
        $this->assertSame(20, Service::whereIn('service_category_id', $categoryIds)->count());
        $this->assertSame(4, ServiceCategory::count());
    }
}
