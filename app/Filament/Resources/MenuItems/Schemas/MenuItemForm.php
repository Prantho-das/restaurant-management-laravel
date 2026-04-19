<?php

namespace App\Filament\Resources\MenuItems\Schemas;

use App\Models\Ingredient;
use App\Models\MenuItem;
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
                    ->unique(MenuItem::class, 'slug', ignoreRecord: true),
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
                    ->disk('public')
                    ->image(),
                Toggle::make('is_active')
                    ->required()
                    ->default(true),
                Select::make('preparation_type')
                    ->options([
                        'made-to-order' => 'Made-to-order (Cooked on demand)',
                        'premade' => 'Pre-made (Batch Cooking)',
                    ])
                    ->default('made-to-order')
                    ->required()
                    ->live()
                    ->helperText('Pre-made items deduct ingredients during "Food Preparation". Made-to-order items deduct ingredients during "Order completion".'),
                TextInput::make('sku')
                    ->label('SKU'),
                Section::make('Recipe')
                    ->description('Define the ingredients required for this menu item.')
                    ->visible(fn ($get) => $get('preparation_type') === 'made-to-order')
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
