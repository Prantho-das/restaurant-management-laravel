<?php

namespace App\Filament\Resources\FoodPreparations\Schemas;

use App\Models\Ingredient;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FoodPreparationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('menu_item_id')
                    ->label('Menu Item')
                    ->relationship(
                        'menuItem',
                        'name',
                        fn ($query) => $query->where('preparation_type', 'premade')
                    )
                    ->required()
                    ->searchable()
                    ->preload()
                    ->helperText('Only items marked as "Pre-made" in Menu Items can be prepared in advance.'),
                TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->minValue(0.0001)
                    ->step(0.0001),
                DateTimePicker::make('prepared_at')
                    ->default(now())
                    ->required(),
                Textarea::make('notes')
                    ->columnSpanFull(),
                Section::make('Ingredients')
                    ->description('Select the ingredients used in this preparation batch. This will deduct them from inventory.')
                    ->schema([
                        Repeater::make('items')
                            ->relationship('items')
                            ->schema([
                                Select::make('ingredient_id')
                                    ->relationship('ingredient', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                                TextInput::make('quantity')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0.0001)
                                    ->step(0.0001)
                                    ->suffix(fn ($get) => Ingredient::find($get('ingredient_id'))?->unit ?? 'unit'),
                            ])
                            ->columns(2)
                            ->itemLabel(fn (array $state): ?string => Ingredient::find($state['ingredient_id'])?->name ?? null),
                    ]),
            ]);
    }
}
