<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class QuickActions extends Widget
{
    protected static ?int $sort = 0;

    protected string $view = 'filament.widgets.quick-actions';

    protected int|string|array $columnSpan = 'full';

    public function getActions(): array
    {
        return [
            [
                'label' => 'POS System',
                'description' => 'Open the point of sale terminal',
                'icon' => 'heroicon-o-shopping-cart',
                'color' => 'primary',
                'url' => route('filament.admin.pages.pos'),
            ],
            [
                'label' => 'New Order',
                'description' => 'Create a new manual order',
                'icon' => 'heroicon-o-plus-circle',
                'color' => 'success',
                'url' => route('filament.admin.resources.orders.create'),
            ],
            [
                'label' => 'Add Expense',
                'description' => 'Record a new business expense',
                'icon' => 'heroicon-o-minus-circle',
                'color' => 'danger',
                'url' => route('filament.admin.resources.expenses.create'),
            ],
            [
                'label' => 'Reservations',
                'description' => 'Manage table bookings',
                'icon' => 'heroicon-o-calendar',
                'color' => 'warning',
                'url' => route('filament.admin.resources.reservations.index'),
            ],
            [
                'label' => 'Staff Payroll',
                'description' => 'Process employee salaries',
                'icon' => 'heroicon-o-banknotes',
                'color' => 'info',
                'url' => route('filament.admin.resources.payrolls.index'),
            ],
            [
                'label' => 'Sales Reports',
                'description' => 'View detailed sales analytics',
                'icon' => 'heroicon-o-document-chart-bar',
                'color' => 'success',
                'url' => route('filament.admin.pages.reports'),
            ],
        ];
    }
}
