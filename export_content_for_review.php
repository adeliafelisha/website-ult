<?php

use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

echo json_encode([
    'categories' => App\Models\ServiceCategory::orderBy('sort_order')->get()->toArray(),
    'services' => App\Models\Service::with('category')->orderBy('id')->get()->toArray(),
    'articles' => App\Models\Article::orderBy('id')->get()->toArray(),
    'faqs' => App\Models\Faq::orderBy('sort_order')->get()->toArray(),
    'links' => App\Models\QuickLink::orderBy('sort_order')->get()->toArray(),
    'contacts' => App\Models\Contact::orderBy('sort_order')->get()->toArray(),
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
