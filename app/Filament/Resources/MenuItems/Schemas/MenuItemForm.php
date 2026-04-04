<?php

namespace App\Filament\Resources\MenuItems\Schemas;

use App\Models\Ingredient;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class MenuItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required(),
                Select::make('outlet_id')
                    ->relationship('outlet', 'name'),
                TextInput::make('name')
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (string $operation, $state, $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('base_price')
                    ->required()
                    ->numeric()
                    ->prefix('৳ '),
                TextInput::make('discount_price')
                    ->numeric()
                    ->prefix('৳ '),
                TextInput::make('tax_rate')
                    ->required()
                    ->numeric()
                    ->default(0),
                FileUpload::make('image')
                    ->image(),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('sku')
                    ->label('SKU'),
                Section::make('Recipe')
                    ->description('Define the ingredients required for this menu item.')
                    ->schema([
                        Repeater::make('recipes')
                            ->relationship('recipes')
                            ->schema([
                                Select::make('ingredient_id')
                                    ->relationship('ingredient', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                                TextInput::make('quantity')
                                    ->required()
                                    ->numeric()
                                    ->suffix(fn ($get) => Ingredient::find($get('ingredient_id'))?->unit ?? 'unit'),
                            ])
                            ->columns(2)
                            ->itemLabel(fn (array $state): ?string => Ingredient::find($state['ingredient_id'])?->name ?? null),
                    ]),
            ]);
    }
}
