<?php

namespace App\Filament\Resources\Inventory\StockAdjustments\Pages;

use App\Filament\Resources\Inventory\StockAdjustments\StockAdjustmentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStockAdjustment extends EditRecord
{
    protected static string $resource = StockAdjustmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
