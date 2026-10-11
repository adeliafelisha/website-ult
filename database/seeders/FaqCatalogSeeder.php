<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FaqCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $catalog = json_decode((string) file_get_contents(database_path('data/faq-catalog.json')), true, flags: JSON_THROW_ON_ERROR);

        DB::transaction(function () use ($catalog): void {
            Faq::query()->delete();
            foreach ($catalog as $item) {
                unset($item['source_row']);
                Faq::create($item);
            }
        });
    }
}
