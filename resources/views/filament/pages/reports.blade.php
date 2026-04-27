<x-filament-panels::page>
    @vite('resources/css/app.css')
    <div class="mb-8">
        {{ $this->form }}
    </div>

    @php
    $reports = [
    ['name' => 'Sales Summary', 'slug' => 'sales-summary', 'icon' => 'heroicon-o-banknotes', 'color' => 'info', 'desc' => 'Revenue, tax, and order volume overview.'],
    ['name' => 'Product Performance', 'slug' => 'product-performance', 'icon' => 'heroicon-o-presentation-chart-line', 'color' => 'success', 'desc' => 'Ranking items by quantity and revenue.'],
    ['name' => 'Inventory & Wastage', 'slug' => 'inventory-wastage', 'icon' => 'heroicon-o-archive-box', 'color' => 'warning', 'desc' => 'Current stock and registered wastage.'],
    ['name' => 'Profit & Loss', 'slug' => 'profit-loss', 'icon' => 'heroicon-o-calculator', 'color' => 'danger', 'desc' => 'Financial health: Sales vs Expenses.'],
    ['name' => 'Staff Performance', 'slug' => 'staff-performance', 'icon' => 'heroicon-o-users', 'color' => 'gray', 'desc' => 'Sales and order metrics per employee.'],
    ['name' => 'Purchases Report', 'slug' => 'purchases', 'icon' => 'heroicon-o-shopping-cart', 'color' => 'info', 'desc' => 'Inventory procurement and supplier costs.'],
    ['name' => 'Expense Report', 'slug' => 'expenses', 'icon' => 'heroicon-o-currency-dollar', 'color' => 'danger', 'desc' => 'Detailed list of all operational expenses.'],
    ['name' => 'Stock Adjustments', 'slug' => 'stock-adjustments', 'icon' => 'heroicon-o-adjustments-horizontal', 'color' => 'warning', 'desc' => 'Manual stock corrections and audit trail.'],
    ['name' => 'Wastage Report', 'slug' => 'wastage', 'icon' => 'heroicon-o-trash', 'color' => 'danger', 'desc' => 'Registered wastage of ingredients and menu items.'],
    ['name' => 'Cash Flow', 'slug' => 'cash-flow', 'icon' => 'heroicon-o-arrows-right-left', 'color' => 'success', 'desc' => 'Net cash flow and balance overview.'],
    ['name' => 'Category Report', 'slug' => 'category', 'icon' => 'heroicon-o-tag', 'color' => 'info', 'desc' => 'Sales performance by menu categories.'],
    ['name' => 'Waiter Report', 'slug' => 'waiter', 'icon' => 'heroicon-o-user-group', 'color' => 'success', 'desc' => 'Sales and efficiency metrics per waiter.'],
    ['name' => 'Due Report', 'slug' => 'due', 'icon' => 'heroicon-o-clock', 'color' => 'warning', 'desc' => 'Orders with pending payments.'],
    ['name' => 'Discount Report', 'slug' => 'discount', 'icon' => 'heroicon-o-gift', 'color' => 'danger', 'desc' => 'Overview of discounts and promotions applied.'],
    ['name' => 'Channel Report', 'slug' => 'channel', 'icon' => 'heroicon-o-truck', 'color' => 'info', 'desc' => 'Sales distribution by order channel (Delivery, Dine-in, etc).'],
    ];

    $languages = [
        ['code' => 'en', 'name' => 'English', 'flag' => 'EN'],
        ['code' => 'bn', 'name' => 'বাংলা', 'flag' => 'বাং'],
    ];
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($reports as $report)
        <div
            class="flex flex-col p-6 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm hover:shadow-md transition-all duration-200">

            <div class="flex items-start gap-4 mb-6">
                {{-- Icon with explicit background mapping --}}
                <div @class([ 'flex items-center justify-center shrink-0 w-12 h-12 rounded-lg'
                    , 'bg-info-50 text-info-600 dark:bg-info-500/10 dark:text-info-400'=> $report['color'] === 'info',
                    'bg-success-50 text-success-600 dark:bg-success-500/10 dark:text-success-400' => $report['color']
                    === 'success',
                    'bg-warning-50 text-warning-600 dark:bg-warning-500/10 dark:text-warning-400' => $report['color']
                    === 'warning',
                    'bg-danger-50 text-danger-600 dark:bg-danger-500/10 dark:text-danger-400' => $report['color'] ===
                    'danger',
                    'bg-gray-50 text-gray-600 dark:bg-gray-500/10 dark:text-gray-400' => $report['color'] === 'gray',
                    ])>
                    <x-filament::icon icon="{{ $report['icon'] }}" class="h-6 w-6" />
                </div>

                <div>
                    <h3 class="text-base font-bold text-gray-950 dark:text-white leading-6">
                        {{ $report['name'] }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ $report['desc'] }}
                    </p>
                </div>
            </div>

            <div class="mt-auto">
                <x-filament::button tag="a" :color="$report['color']" variant="soft"
                    x-bind:href="'{{ route('reports.' . $report['slug']) }}?start_date=' + $wire.data.start_date + '&end_date=' + $wire.data.end_date"
                    target="_blank" icon="heroicon-m-arrow-down-tray" class="w-full justify-center">
                    Download
                </x-filament::button>
            </div>
        </div>
        @endforeach
    </div>
</x-filament-panels::page>
