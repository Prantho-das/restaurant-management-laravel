<?php

namespace App\Filament\Resources\Wastages\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class WastageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Wastage Details')
                    ->schema([
                        Select::make('ingredient_id')
                            ->label('Ingredient')
                            ->relationship('ingredient', 'name')
                            ->searchable()
                            ->preload()
                            ->helperText('Select the ingredient that was wasted.'),
                        Select::make('menu_item_id')
                            ->label('Menu Item')
                            ->relationship('menuItem', 'name')
                            ->searchable()
                            ->preload()
                            ->helperText('Or select the prepared menu item that was wasted.'),
                        TextInput::make('quantity')
                            ->required()
                            ->numeric()
                            ->minValue(0.001),
                        Select::make('unit')
                            ->options([
                                'kg' => 'Kilogram (kg)',
                                'g' => 'Gram (g)',
                                'l' => 'Liter (l)',
                                'ml' => 'Milliliter (ml)',
                                'pcs' => 'Pieces (pcs)',
                                'plate' => 'Plate',
                                'portion' => 'Portion',
                            ])
                            ->required()
                            ->searchable(),
                    ])
                    ->columns(2),
                Section::make('Reason & Cost')
                    ->schema([
                        Select::make('reason')
                            ->options([
                                'expired' => 'Expired',
                                'damaged' => 'Damaged',
                                'spillage' => 'Spillage',
                                'preparation_error' => 'Preparation Error',
                                'quality_issue' => 'Quality Issue',
                                'other' => 'Other',
                            ])
                            ->required()
                            ->searchable(),
                        DatePicker::make('date')
                            ->required()
                            ->default(now()),
                        TextInput::make('estimated_cost')
                            ->label('Estimated Cost')
                            ->numeric()
                            ->prefix('৳')
                            ->default(0)
                            ->minValue(0),
                    ])
                    ->columns(3),
                Section::make('Notes')
                    ->schema([
                        Textarea::make('notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }
}
