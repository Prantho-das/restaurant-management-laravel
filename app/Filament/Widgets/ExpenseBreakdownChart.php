<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class ExpenseBreakdownChart extends ChartWidget
{
    protected ?string $heading = 'Expenses by Category (This Month)';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 1;

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $expenses = Expense::whereMonth('date', Carbon::now()->month)
            ->whereYear('date', Carbon::now()->year)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        $categoryLabels = [
            'rent' => 'Rent',
            'utilities' => 'Utilities',
            'salary' => 'Salary',
            'supplies' => 'Supplies',
            'maintenance' => 'Maintenance',
            'marketing' => 'Marketing',
            'other' => 'Other',
        ];

        $colors = [
            'rent' => '#808000',
            'utilities' => '#c5a059',
            'salary' => '#4f9d69',
            'supplies' => '#5b8fb9',
            'maintenance' => '#b05e5e',
            'marketing' => '#9b6fb5',
            'other' => '#888888',
        ];

        $labels = [];
        $data = [];
        $backgroundColors = [];

        foreach ($expenses as $category => $total) {
            $labels[] = $categoryLabels[$category] ?? ucfirst($category);
            $data[] = (float) $total;
            $backgroundColors[] = $colors[$category] ?? '#888888';
        }

        if (empty($data)) {
            $labels = ['No expenses'];
            $data = [1];
            $backgroundColors = ['#e5e7eb'];
        }

        return [
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => $backgroundColors,
                    'borderWidth' => 0,
                    'hoverOffset' => 8,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
