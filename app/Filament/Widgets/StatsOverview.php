<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use App\Models\Ingredient;
use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $todayRevenue = Order::whereDate('created_at', Carbon::today())
            ->where('status', 'completed')
            ->sum('total_amount');

        $yesterdayRevenue = Order::whereDate('created_at', Carbon::yesterday())
            ->where('status', 'completed')
            ->sum('total_amount');

        $revenueChange = $yesterdayRevenue > 0
            ? round((($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100, 1)
            : 0;

        $todayOrders = Order::whereDate('created_at', Carbon::today())->count();
        $yesterdayOrders = Order::whereDate('created_at', Carbon::yesterday())->count();
        $ordersChange = $yesterdayOrders > 0
            ? round((($todayOrders - $yesterdayOrders) / $yesterdayOrders) * 100, 1)
            : 0;

        $monthlyExpenses = Expense::whereMonth('date', Carbon::now()->month)
            ->whereYear('date', Carbon::now()->year)
            ->sum('amount');

        $lastMonthExpenses = Expense::whereMonth('date', Carbon::now()->subMonth()->month)
            ->whereYear('date', Carbon::now()->subMonth()->year)
            ->sum('amount');

        $expenseChange = $lastMonthExpenses > 0
            ? round((($monthlyExpenses - $lastMonthExpenses) / $lastMonthExpenses) * 100, 1)
            : 0;

        $lowStockCount = Ingredient::whereColumn('current_stock', '<=', 'alert_threshold')->count();

        $sparklineRevenue = Order::whereDate('created_at', '>=', Carbon::today()->subDays(6))
            ->where('status', 'completed')
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue')
            ->groupBy('date')
            ->pluck('revenue', 'date');

        $last7DaysRevenue = collect(range(6, 0))->map(function ($daysAgo) use ($sparklineRevenue) {
            $date = Carbon::today()->subDays($daysAgo)->toDateString();

            return (float) ($sparklineRevenue[$date] ?? 0);
        })->toArray();

        $sparklineOrders = Order::whereDate('created_at', '>=', Carbon::today()->subDays(6))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $last7DaysOrders = collect(range(6, 0))->map(function ($daysAgo) use ($sparklineOrders) {
            $date = Carbon::today()->subDays($daysAgo)->toDateString();

            return (int) ($sparklineOrders[$date] ?? 0);
        })->toArray();

        return [
            Stat::make('Today\'s Revenue', '৳'.number_format($todayRevenue, 2))
                ->description($revenueChange >= 0 ? "{$revenueChange}% increase" : abs($revenueChange).'% decrease')
                ->descriptionIcon($revenueChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart($last7DaysRevenue)
                ->color($revenueChange >= 0 ? 'success' : 'danger'),

            Stat::make('Today\'s Orders', $todayOrders)
                ->description($ordersChange >= 0 ? "{$ordersChange}% increase" : abs($ordersChange).'% decrease')
                ->descriptionIcon($ordersChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart($last7DaysOrders)
                ->color($ordersChange >= 0 ? 'success' : 'danger'),

            Stat::make('Monthly Expenses', '৳'.number_format($monthlyExpenses, 2))
                ->description($expenseChange >= 0 ? "{$expenseChange}% vs last month" : abs($expenseChange).'% vs last month')
                ->descriptionIcon($expenseChange <= 0 ? 'heroicon-m-arrow-trending-down' : 'heroicon-m-arrow-trending-up')
                ->color($expenseChange <= 0 ? 'success' : 'warning'),

            Stat::make('Low Stock Alerts', $lowStockCount)
                ->description($lowStockCount > 0 ? 'Ingredients need restocking' : 'All stocked')
                ->descriptionIcon($lowStockCount > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($lowStockCount > 0 ? 'danger' : 'success'),
        ];
    }
}
