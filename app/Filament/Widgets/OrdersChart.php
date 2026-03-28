<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class OrdersChart extends ChartWidget
{
    protected ?string $heading = 'Orders by Type (Last 7 Days)';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 1;

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $days = collect(range(6, 0))->map(fn ($daysAgo) => Carbon::today()->subDays($daysAgo));
        $labels = $days->map(fn ($date) => $date->format('D, d'))->toArray();

        $ordersByDateAndType = Order::whereDate('created_at', '>=', Carbon::today()->subDays(6))
            ->selectRaw('DATE(created_at) as date, order_type, COUNT(*) as total')
            ->groupBy('date', 'order_type')
            ->get()
            ->groupBy('order_type');

        $types = ['dine_in' => 'Dine In', 'takeaway' => 'Takeaway', 'delivery' => 'Delivery'];
        $colors = ['dine_in' => '#808000', 'takeaway' => '#c5a059', 'delivery' => '#4f9d69'];

        $datasets = [];
        foreach ($types as $type => $label) {
            $typeData = $ordersByDateAndType->get($type, collect())->keyBy('date');

            $data = $days->map(function ($date) use ($typeData) {
                return (int) ($typeData[$date->toDateString()]->total ?? 0);
            })->toArray();

            $datasets[] = [
                'label' => $label,
                'data' => $data,
                'backgroundColor' => $colors[$type],
                'borderRadius' => 6,
            ];
        }

        return [
            'datasets' => $datasets,
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
