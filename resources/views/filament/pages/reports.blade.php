<x-filament-panels::page>
    <div class="mb-8">
        {{ $this->form }}
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @php
            $reports = [
                ['name' => 'Sales Summary', 'slug' => 'sales-summary', 'icon' => 'heroicon-o-banknotes', 'color' => 'primary', 'desc' => 'Revenue, tax, and order volume overview.'],
                ['name' => 'Product Performance', 'slug' => 'product-performance', 'icon' => 'heroicon-o-presentation-chart-line', 'color' => 'success', 'desc' => 'Ranking items by quantity and revenue.'],
                ['name' => 'Inventory & Wastage', 'slug' => 'inventory-wastage', 'icon' => 'heroicon-o-archive-box', 'color' => 'warning', 'desc' => 'Current stock and registered wastage.'],
                ['name' => 'Profit & Loss', 'slug' => 'profit-loss', 'icon' => 'heroicon-o-calculator', 'color' => 'danger', 'desc' => 'Financial health: Sales vs Expenses.'],
                ['name' => 'Staff Performance', 'slug' => 'staff-performance', 'icon' => 'heroicon-o-users', 'color' => 'info', 'desc' => 'Sales and order metrics per employee.'],
            ];
        @endphp

        @foreach($reports as $report)
            <div class="fi-section rounded-2xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-8 flex flex-col items-center text-center transition-all hover:ring-gray-950/10 dark:hover:ring-white/20">
                <div @class([
                    'rounded-full mb-5 flex items-center justify-center w-20 h-20 shrink-0 shadow-sm',
                    'bg-primary-50 text-primary-600 dark:bg-primary-400/10 dark:text-primary-400' => $report['color'] === 'primary',
                    'bg-success-50 text-success-600 dark:bg-success-400/10 dark:text-success-400' => $report['color'] === 'success',
                    'bg-warning-50 text-warning-600 dark:bg-warning-400/10 dark:text-warning-400' => $report['color'] === 'warning',
                    'bg-danger-50 text-danger-600 dark:bg-danger-400/10 dark:text-danger-400' => $report['color'] === 'danger',
                    'bg-info-50 text-info-600 dark:bg-info-400/10 dark:text-info-400' => $report['color'] === 'info',
                ])>
                    <x-filament::icon
                        icon="{{ $report['icon'] }}"
                        class="h-10 w-10 stroke-2"
                    />
                </div>
                
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ $report['name'] }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6 px-4">{{ $report['desc'] }}</p>
                
                <div class="mt-auto w-full">
                    <x-filament::button
                        color="{{ $report['color'] }}"
                        tag="a"
                        x-bind:href="'{{ route('reports.' . $report['slug']) }}?start_date=' + $wire.data.start_date + '&end_date=' + $wire.data.end_date"
                        target="_blank"
                        icon="heroicon-m-arrow-down-tray"
                        class="w-full"
                    >
                        Download PDF
                    </x-filament::button>
                </div>
            </div>
        @endforeach
    </div>

    <style>
        /* Custom styles if needed for the WOW effect */
        .fi-section {
            transition: transform 0.2s ease-in-out;
        }
        .fi-section:hover {
            transform: translateY(-4px);
        }
    </style>
</x-filament-panels::page>
