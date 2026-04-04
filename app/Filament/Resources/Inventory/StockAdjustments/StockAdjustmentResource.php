<?php

namespace App\Filament\Resources\Inventory\StockAdjustments;

use App\Filament\Resources\Inventory\StockAdjustments\Pages\CreateStockAdjustment;
use App\Filament\Resources\Inventory\StockAdjustments\Pages\EditStockAdjustment;
use App\Filament\Resources\Inventory\StockAdjustments\Pages\ListStockAdjustments;
use App\Filament\Resources\Inventory\StockAdjustments\Schemas\StockAdjustmentForm;
use App\Filament\Resources\Inventory\StockAdjustments\Tables\StockAdjustmentsTable;
use App\Models\StockAdjustment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class StockAdjustmentResource extends Resource
{
    protected static ?string $model = StockAdjustment::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static UnitEnum|string|null $navigationGroup = 'Inventory';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return StockAdjustmentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StockAdjustmentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockAdjustments::route('/'),
            'create' => CreateStockAdjustment::route('/create'),
            'edit' => EditStockAdjustment::route('/{record}/edit'),
        ];
    }
}
