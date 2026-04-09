<?php

namespace App\Filament\Resources\Orders\Exports;

use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class OrderExporter extends Exporter
{
    protected static ?string $disk = 'public';

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('order_number'),
            ExportColumn::make('status'),
            ExportColumn::make('order_type'),
            ExportColumn::make('customer_name'),
            ExportColumn::make('customer_phone'),
            ExportColumn::make('subtotal_amount'),
            ExportColumn::make('discount_amount'),
            ExportColumn::make('total_amount'),
            ExportColumn::make('payment_method'),
            ExportColumn::make('table_number'),
            ExportColumn::make('created_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Your sales export has completed and ' . number_format($export->successful_rows) . ' rows were exported.';
    }
}