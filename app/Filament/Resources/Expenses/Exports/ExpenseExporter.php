<?php

namespace App\Filament\Resources\Expenses\Exports;

use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ExpenseExporter extends Exporter
{
    protected static ?string $disk = 'public';

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('date'),
            ExportColumn::make('category'),
            ExportColumn::make('title'),
            ExportColumn::make('description'),
            ExportColumn::make('amount'),
            ExportColumn::make('payment_method'),
            ExportColumn::make('reference_no'),
            ExportColumn::make('user.name'),
            ExportColumn::make('created_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Your expense export has completed and ' . number_format($export->successful_rows) . ' rows were exported.';
    }
}