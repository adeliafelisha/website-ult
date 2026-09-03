<?php

namespace App\Filament\Widgets;

use App\Models\PageView;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class TrafficInsights extends Widget
{
    protected static bool $isLazy = false;

    protected static string $view = 'filament.widgets.traffic-insights';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $since = now()->subDays(29)->startOfDay();

        return [
            'topPages' => PageView::query()
                ->selectRaw('path, COUNT(*) as total')
                ->where('viewed_at', '>=', $since)
                ->groupBy('path')
                ->orderByDesc('total')
                ->limit(6)
                ->get(),
            'devices' => $this->breakdown('device_type', $since),
            'sources' => $this->sources($since),
        ];
    }

    private function breakdown(string $column, mixed $since): Collection
    {
        return PageView::query()
            ->selectRaw($column.', COUNT(*) as total')
            ->where('viewed_at', '>=', $since)
            ->groupBy($column)
            ->orderByDesc('total')
            ->get();
    }

    private function sources(mixed $since): Collection
    {
        $sources = PageView::query()
            ->selectRaw("COALESCE(referrer_host, 'Langsung / internal') as source, COUNT(*) as total")
            ->where('viewed_at', '>=', $since)
            ->groupBy('referrer_host')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        return $sources;
    }
}
