<?php

namespace App\Filament\Resources\FoodPreparations;

use App\Filament\Resources\FoodPreparations\Pages\CreateFoodPreparation;
use App\Filament\Resources\FoodPreparations\Pages\EditFoodPreparation;
use App\Filament\Resources\FoodPreparations\Pages\ListFoodPreparations;
use App\Filament\Resources\FoodPreparations\Schemas\FoodPreparationForm;
use App\Filament\Resources\FoodPreparations\Tables\FoodPreparationsTable;
use App\Models\FoodPreparation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class FoodPreparationResource extends Resource
{
    protected static ?string $model = FoodPreparation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFire;

    protected static string|UnitEnum|null $navigationGroup = 'Menu';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return FoodPreparationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FoodPreparationsTable::configure($table);
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
            'index' => ListFoodPreparations::route('/'),
            'create' => CreateFoodPreparation::route('/create'),
            'edit' => EditFoodPreparation::route('/{record}/edit'),
        ];
    }
}
