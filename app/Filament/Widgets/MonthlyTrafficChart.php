<?php

namespace App\Filament\Widgets;

use App\Models\PageView;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class MonthlyTrafficChart extends ChartWidget
{
    protected static bool $isLazy = false;

    protected static ?string $heading = 'Trafik 12 Bulan Terakhir';

    protected static ?string $description = 'Perbandingan tayangan halaman dan pengunjung unik setiap bulan.';

    protected static ?int $sort = 2;

    protected static ?string $maxHeight = '320px';

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $start = now()->subMonths(11)->startOfMonth();
        $periodExpression = match (DB::connection()->getDriverName()) {
            'sqlite' => "strftime('%Y-%m', viewed_at)",
            'pgsql' => "to_char(viewed_at, 'YYYY-MM')",
            default => "DATE_FORMAT(viewed_at, '%Y-%m')",
        };
        $traffic = PageView::query()
            ->selectRaw("{$periodExpression} as period, COUNT(*) as total, COUNT(DISTINCT visitor_hash) as unique_total")
            ->where('viewed_at', '>=', $start)
            ->groupByRaw($periodExpression)
            ->get()
            ->keyBy('period');
        $months = collect(range(11, 0))->map(fn (int $offset) => now()->subMonths($offset)->startOfMonth());

        return [
            'datasets' => [
                [
                    'label' => 'Tayangan halaman',
                    'data' => $months->map(fn ($month) => (int) ($traffic->get($month->format('Y-m'))?->total ?? 0))->all(),
                    'borderColor' => '#9333ea',
                    'backgroundColor' => 'rgba(147, 51, 234, 0.16)',
                    'fill' => true,
                    'tension' => 0.35,
                ],
                [
                    'label' => 'Pengunjung unik',
                    'data' => $months->map(fn ($month) => (int) ($traffic->get($month->format('Y-m'))?->unique_total ?? 0))->all(),
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.10)',
                    'tension' => 0.35,
                ],
            ],
            'labels' => $months->map(fn ($month) => $month->locale('id')->translatedFormat('M Y'))->all(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
