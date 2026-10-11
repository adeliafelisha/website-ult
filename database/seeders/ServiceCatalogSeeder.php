<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $catalogs = json_decode(file_get_contents(database_path('data/service-catalog.json')), true, flags: JSON_THROW_ON_ERROR);

        DB::transaction(function () use ($catalogs): void {
            $categorySlugs = collect($catalogs)->pluck('category.slug')->all();
            ServiceCategory::query()->whereNotIn('slug', $categorySlugs)->delete();

            foreach ($catalogs as $catalog) {
                $category = ServiceCategory::updateOrCreate(
                    ['slug' => $catalog['category']['slug']],
                    $catalog['category'],
                );

                foreach ($catalog['services'] as $record) {
                    unset($record['category_slug'], $record['group'], $record['image_count']);
                    if (empty($record['contact_buttons'])) {
                        $record['contact_buttons'] = [[
                            'label' => 'Hubungi Admin ULT',
                            'label_en' => 'Contact ULT Admin',
                            'channel' => 'whatsapp',
                            'url' => Contact::query()->where('type', 'whatsapp')->where('is_published', true)->orderBy('sort_order')->value('url') ?: 'https://wa.me/6280000000000',
                        ]];
                    }
                    $record['service_category_id'] = $category->id;
                    Service::updateOrCreate(['slug' => $record['slug']], $record);
                }
            }
        });
    }
}
