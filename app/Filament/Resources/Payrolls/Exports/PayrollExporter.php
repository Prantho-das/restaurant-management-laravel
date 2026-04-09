<?php

namespace App\Filament\Resources\Payrolls\Exports;

use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PayrollExporter extends Exporter
{
    public static function getColumns(): array
    {
        return [
            ExportColumn::make('user.name'),
            ExportColumn::make('month'),
            ExportColumn::make('year'),
            ExportColumn::make('base_salary'),
            ExportColumn::make('bonus_amount'),
            ExportColumn::make('deduction_amount'),
            ExportColumn::make('advance_amount'),
            ExportColumn::make('net_paid'),
            ExportColumn::make('payment_date'),
            ExportColumn::make('payment_method'),
            ExportColumn::make('status'),
            ExportColumn::make('notes'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Your payroll export has completed and ' . number_format($export->successful_rows) . ' rows were exported.';
    }
}