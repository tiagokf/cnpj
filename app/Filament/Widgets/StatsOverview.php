<?php

namespace App\Filament\Widgets;

use App\Models\CnpjQuery;
use App\Models\PageVisit;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $today = now()->startOfDay();
        $last30 = now()->subDays(30);

        $totalVisits = PageVisit::where('visited_at', '>=', $last30)->count();
        $uniqueVisitors = PageVisit::where('visited_at', '>=', $last30)
            ->distinct('ip_address')
            ->count('ip_address');

        $totalQueries = CnpjQuery::where('queried_at', '>=', $last30)->count();
        $successQueries = CnpjQuery::where('queried_at', '>=', $last30)->where('success', true)->count();
        $successRate = $totalQueries > 0 ? round(($successQueries / $totalQueries) * 100, 1) : 0;

        $avgResponseTime = (int) CnpjQuery::where('queried_at', '>=', $last30)
            ->where('success', true)
            ->avg('response_time_ms');

        // Sparklines dos últimos 7 dias
        $visitsChart = $this->getSparkline(PageVisit::class, 'visited_at', 7);
        $queriesChart = $this->getSparkline(CnpjQuery::class, 'queried_at', 7);

        return [
            Stat::make('Visitas (30d)', number_format($totalVisits, 0, ',', '.'))
                ->description($this->getTodayCount(PageVisit::class, 'visited_at') . ' hoje')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart($visitsChart)
                ->color('primary'),

            Stat::make('Visitantes Únicos', number_format($uniqueVisitors, 0, ',', '.'))
                ->description('Últimos 30 dias')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('Consultas CNPJ', number_format($totalQueries, 0, ',', '.'))
                ->description($this->getTodayCount(CnpjQuery::class, 'queried_at') . ' hoje')
                ->descriptionIcon('heroicon-m-magnifying-glass')
                ->chart($queriesChart)
                ->color('success'),

            Stat::make('Taxa de Sucesso', $successRate . '%')
                ->description($successQueries . ' de ' . $totalQueries)
                ->descriptionIcon('heroicon-m-check-circle')
                ->color($successRate >= 80 ? 'success' : ($successRate >= 50 ? 'warning' : 'danger')),

            Stat::make('Tempo Médio', $avgResponseTime . 'ms')
                ->description('Resposta das APIs')
                ->descriptionIcon('heroicon-m-clock')
                ->color($avgResponseTime <= 2000 ? 'success' : 'warning'),
        ];
    }

    private function getSparkline(string $model, string $dateColumn, int $days): array
    {
        $data = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            $data[] = $model::whereDate($dateColumn, $date)->count();
        }

        return $data;
    }

    private function getTodayCount(string $model, string $dateColumn): int
    {
        return $model::whereDate($dateColumn, today())->count();
    }
}
