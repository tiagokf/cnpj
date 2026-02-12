<?php

namespace App\Filament\Widgets;

use App\Models\PageVisit;
use Filament\Widgets\ChartWidget;

class LocationChart extends ChartWidget
{
    protected static ?int $sort = 6;

    protected int|string|array $columnSpan = ['lg' => 1, 'default' => 'full'];

    protected ?string $maxHeight = '300px';

    public ?string $filter = 'state';

    public function getHeading(): string
    {
        $headings = [
            'state' => 'Estados',
            'city' => 'Cidades',
            'country' => 'Países',
        ];

        return $headings[$this->filter] ?? 'Estados';
    }

    protected function getFilters(): ?array
    {
        return [
            'state' => 'Estados',
            'city' => 'Cidades',
            'country' => 'Países',
        ];
    }

    protected function getData(): array
    {
        $column = match ($this->filter) {
            'state', 'city', 'country' => $this->filter,
            default => 'state',
        };

        $data = PageVisit::query()
            ->selectRaw("`{$column}` as label, COUNT(*) as total")
            ->whereNotNull($column)
            ->groupBy($column)
            ->orderByDesc('total')
            ->limit(8)
            ->pluck('total', 'label');

        $colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6', '#f97316'];

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
