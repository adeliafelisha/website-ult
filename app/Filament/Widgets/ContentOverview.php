<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ArticleResource;
use App\Filament\Resources\FaqResource;
use App\Filament\Resources\SatisfactionSurveyResource;
use App\Filament\Resources\ServiceResource;
use App\Models\Article;
use App\Models\Faq;
use App\Models\SatisfactionSurvey;
use App\Models\Service;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ContentOverview extends BaseWidget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = 3;

    protected ?string $heading = 'Status Konten';

    protected function getStats(): array
    {
        return [
            Stat::make('Layanan aktif', Service::where('is_published', true)->count().' / '.Service::count())
                ->description('Klik untuk mengelola layanan')
                ->url(ServiceResource::getUrl('index'))
                ->color('primary'),
            Stat::make('Artikel terbit', Article::where('is_published', true)->count().' / '.Article::count())
                ->description('Klik untuk mengelola artikel')
                ->url(ArticleResource::getUrl('index'))
                ->color('success'),
            Stat::make('FAQ aktif', Faq::where('is_published', true)->count().' / '.Faq::count())
                ->description('Klik untuk mengelola FAQ')
                ->url(FaqResource::getUrl('index'))
                ->color('info'),
            Stat::make('Data survei terbit', SatisfactionSurvey::where('is_published', true)->count().' / '.SatisfactionSurvey::count())
                ->description('Klik untuk mengelola skor dan tautan survei')
                ->url(SatisfactionSurveyResource::getUrl('index'))
                ->color('warning'),
        ];
    }
}
