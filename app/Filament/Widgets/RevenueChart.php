<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class RevenueChart extends ChartWidget
{
    protected ?string $heading = 'Revenue (Last 30 Days)';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 1;

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $revenueByDate = Order::whereDate('created_at', '>=', Carbon::today()->subDays(29))
            ->where('status', 'completed')
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue')
            ->groupBy('date')
            ->pluck('revenue', 'date');

        $days = collect(range(29, 0))->map(function ($daysAgo) use ($revenueByDate) {
            $date = Carbon::today()->subDays($daysAgo);

            return [
                'label' => $date->format('d M'),
                'revenue' => (float) ($revenueByDate[$date->toDateString()] ?? 0),
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Revenue (৳)',
                    'data' => $days->pluck('revenue')->toArray(),
                    'borderColor' => '#808000',
                    'backgroundColor' => 'rgba(128, 128, 0, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $days->pluck('label')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
