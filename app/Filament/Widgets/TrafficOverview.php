<?php

namespace App\Filament\Widgets;

use App\Models\OutboundClick;
use App\Models\PageView;
use App\Models\SearchEvent;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TrafficOverview extends BaseWidget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = 1;

    protected static ?string $pollingInterval = '60s';

    protected ?string $heading = 'Ringkasan Trafik';

    protected ?string $description = 'Kunjungan halaman publik, tanpa menyimpan alamat IP mentah.';

    protected function getStats(): array
    {
        $monthStart = now()->startOfMonth();
        $previousStart = now()->subMonthNoOverflow()->startOfMonth();
        $previousEnd = now()->subMonthNoOverflow()->endOfMonth();

        $views = PageView::where('viewed_at', '>=', $monthStart)->count();
        $previousViews = PageView::whereBetween('viewed_at', [$previousStart, $previousEnd])->count();
        $change = $previousViews > 0 ? round((($views - $previousViews) / $previousViews) * 100) : null;

        $dailyViews = PageView::where('viewed_at', '>=', now()->subDays(6)->startOfDay())
            ->get(['viewed_at'])
            ->groupBy(fn (PageView $view) => $view->viewed_at->format('Y-m-d'));
        $chart = collect(range(6, 0))->map(fn (int $days) => $dailyViews->get(now()->subDays($days)->format('Y-m-d'), collect())->count())->all();

        return [
            Stat::make('Tayangan bulan ini', number_format($views))
                ->description($change === null ? 'Data pembanding belum tersedia' : abs($change).'% '.($change >= 0 ? 'naik' : 'turun').' dari bulan lalu')
                ->descriptionIcon($change !== null && $change < 0 ? 'heroicon-m-arrow-trending-down' : 'heroicon-m-arrow-trending-up')
                ->color($change !== null && $change < 0 ? 'warning' : 'success')
                ->chart($chart),
            Stat::make('Pengunjung unik', number_format(PageView::where('viewed_at', '>=', $monthStart)->distinct('visitor_hash')->count('visitor_hash')))
                ->description('Perangkat/browser unik bulan ini')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
            Stat::make('Pencarian', number_format(SearchEvent::where('created_at', '>=', $monthStart)->count()))
                ->description('Pencarian internal bulan ini')
                ->descriptionIcon('heroicon-m-magnifying-glass')
                ->color('info'),
            Stat::make('Klik layanan keluar', number_format(OutboundClick::where('created_at', '>=', $monthStart)->count()))
                ->description('Klik menuju sistem eksternal')
                ->descriptionIcon('heroicon-m-arrow-top-right-on-square')
                ->color('gray'),
        ];
    }
}
