<?php

namespace App\Filament\Widgets;

use App\Models\PageVisit;
use Filament\Widgets\ChartWidget;

class BrowserChart extends ChartWidget
{
    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = ['lg' => 1, 'default' => 'full'];

    protected ?string $maxHeight = '300px';

    public ?string $filter = 'browser';

    public function getHeading(): string
    {
        $headings = [
            'browser' => 'Navegadores',
            'platform' => 'Sistemas Operacionais',
            'device_type' => 'Dispositivos',
        ];

        return $headings[$this->filter] ?? 'Navegadores';
    }

    protected function getFilters(): ?array
    {
        return [
            'browser' => 'Navegadores',
            'platform' => 'Sistemas',
            'device_type' => 'Dispositivos',
        ];
    }

    protected function getData(): array
    {
        $column = match ($this->filter) {
            'browser', 'platform', 'device_type' => $this->filter,
            default => 'browser',
        };

        $data = PageVisit::query()
            ->selectRaw("`{$column}` as label, COUNT(*) as total")
            ->whereNotNull($column)
            ->groupBy($column)
            ->orderByDesc('total')
            ->limit(6)
            ->pluck('total', 'label');

        $colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'];

        return [
            'datasets' => [
                [
                    'data' => $data->values()->toArray(),
                    'backgroundColor' => array_slice($colors, 0, $data->count()),
                ],
            ],
            'labels' => $data->keys()->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
