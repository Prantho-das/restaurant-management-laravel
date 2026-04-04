<?php

namespace App\Filament\Resources\Wastages;

use App\Filament\Resources\Wastages\Pages\CreateWastage;
use App\Filament\Resources\Wastages\Pages\EditWastage;
use App\Filament\Resources\Wastages\Pages\ListWastages;
use App\Filament\Resources\Wastages\Schemas\WastageForm;
use App\Filament\Resources\Wastages\Tables\WastagesTable;
use App\Models\Wastage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class WastageResource extends Resource
{
    protected static ?string $model = Wastage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBoxXMark;

    protected static string|UnitEnum|null $navigationGroup = 'Inventory';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return WastageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WastagesTable::configure($table);
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
            'index' => ListWastages::route('/'),
            'create' => CreateWastage::route('/create'),
            'edit' => EditWastage::route('/{record}/edit'),
        ];
    }
}
