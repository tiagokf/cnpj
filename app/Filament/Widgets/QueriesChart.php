<?php

namespace App\Filament\Widgets;

use App\Models\CnpjQuery;
use Filament\Widgets\ChartWidget;

class QueriesChart extends ChartWidget
{
    protected ?string $heading = 'Consultas CNPJ por Dia';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = ['lg' => 1, 'default' => 'full'];

    protected ?string $maxHeight = '300px';

    public ?string $filter = '30';

    protected function getFilters(): ?array
    {
        return [
            '7' => '7 dias',
            '30' => '30 dias',
            '90' => '90 dias',
        ];
    }

    protected function getData(): array
    {
        $days = (int) ($this->filter ?? 30);
        $labels = [];
        $successData = [];
        $failData = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('d/m');
            $successData[] = CnpjQuery::whereDate('queried_at', $date)->where('success', true)->count();
            $failData[] = CnpjQuery::whereDate('queried_at', $date)->where('success', false)->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Sucesso',
                    'data' => $successData,
                    'backgroundColor' => '#10b981',
                ],
                [
                    'label' => 'Falha',
                    'data' => $failData,
                    'backgroundColor' => '#ef4444',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
