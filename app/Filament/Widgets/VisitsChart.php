<?php

namespace App\Filament\Widgets;

use App\Models\PageVisit;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class VisitsChart extends ChartWidget
{
    protected ?string $heading = 'Visitas por Dia';

    protected static ?int $sort = 2;

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
        $data = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('d/m');
            $data[] = PageVisit::whereDate('visited_at', $date)->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Visitas',
                    'data' => $data,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
