<?php

namespace App\Filament\Resources\Purchases\Exports;

use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PurchaseExporter extends Exporter
{
    protected static ?string $disk = 'public';

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('reference_no'),
            ExportColumn::make('supplier.name'),
            ExportColumn::make('purchase_date'),
            ExportColumn::make('status'),
            ExportColumn::make('total_amount'),
            ExportColumn::make('discount'),
            ExportColumn::make('user.name'),
            ExportColumn::make('notes'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Your purchase export has completed and ' . number_format($export->successful_rows) . ' rows were exported.';
    }
}