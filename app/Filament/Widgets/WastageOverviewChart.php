<?php

namespace App\Filament\Widgets;

use App\Models\Wastage;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class WastageOverviewChart extends ChartWidget
{
    protected ?string $heading = 'Wastage by Reason (This Month)';

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 1;

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $wastages = Wastage::whereMonth('date', Carbon::now()->month)
            ->whereYear('date', Carbon::now()->year)
            ->selectRaw('reason, SUM(estimated_cost) as total_cost, COUNT(*) as count')
            ->groupBy('reason')
            ->get()
            ->keyBy('reason');

        $reasons = [
            'expired' => ['label' => 'Expired', 'color' => '#b05e5e'],
            'damaged' => ['label' => 'Damaged', 'color' => '#c5a059'],
            'spillage' => ['label' => 'Spillage', 'color' => '#5b8fb9'],
            'prep_error' => ['label' => 'Prep Error', 'color' => '#9b6fb5'],
            'quality_issue' => ['label' => 'Quality Issue', 'color' => '#808000'],
        ];

        $labels = [];
        $costData = [];
        $colors = [];

        foreach ($reasons as $key => $meta) {
            $labels[] = $meta['label'];
            $costData[] = (float) ($wastages[$key]->total_cost ?? 0);
            $colors[] = $meta['color'];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Cost (৳)',
                    'data' => $costData,
                    'backgroundColor' => $colors,
                    'borderRadius' => 6,
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
