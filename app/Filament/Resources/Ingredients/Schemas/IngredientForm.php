<?php

namespace App\Filament\Resources\Ingredients\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class IngredientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->unique(ignoreRecord: true),
                Select::make('category')
                    ->options([
                        'Food' => 'FoodIngredient',
                        'Supply' => 'Non-Food Supply',
                    ])
                    ->default('Food')
                    ->required(),
                Select::make('unit')
                    ->options([
                        'kg' => 'Kilogram (kg)',
                        'g' => 'Gram (g)',
                        'l' => 'Liter (l)',
                        'ml' => 'Milliliter (ml)',
                        'pcs' => 'Pieces (pcs)',
                        'box' => 'Box',
                        'pkt' => 'Packet',
                    ])
                    ->required()
                    ->searchable(),
                TextInput::make('current_stock')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->helperText('Current stock available in the selected unit.'),
                TextInput::make('alert_threshold')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->helperText('Stock level at which to trigger a low-stock alert.'),
                TextInput::make('unit_cost')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('৳')
                    ->helperText('Unit cost per ingredient for inventory valuation.'),
            ]);
    }
}
