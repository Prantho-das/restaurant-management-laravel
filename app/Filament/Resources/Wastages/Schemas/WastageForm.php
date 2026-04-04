<?php

namespace App\Filament\Resources\Wastages\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class WastageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make('Wastage Item Details')
                            ->description('Select what was wasted and the quantity involved.')
                            ->icon('heroicon-o-archive-box-x-mark')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Select::make('ingredient_id')
                                            ->label('Ingredient')
                                            ->relationship('ingredient', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->helperText('Select the raw ingredient that was wasted.')
                                            ->placeholder('E.g., Chicken, Rice'),
                                        Select::make('menu_item_id')
                                            ->label('Menu Item')
                                            ->relationship('menuItem', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->helperText('Or select the prepared menu item.')
                                            ->placeholder('E.g., Chicken Burger'),
                                    ]),
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('quantity')
                                            ->required()
                                            ->numeric()
                                            ->minValue(0.001)
                                            ->placeholder('0.00'),
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
                                            ->searchable()
                                            ->native(false),
                                    ]),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make('Reason & Impact')
                            ->description('Provide the context and estimated financial loss.')
                            ->icon('heroicon-o-exclamation-triangle')
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
                                    ->searchable()
                                    ->native(false),
                                DatePicker::make('date')
                                    ->required()
                                    ->default(now())
                                    ->native(false),
                                TextInput::make('estimated_cost')
                                    ->label('Estimated Cost')
                                    ->numeric()
                                    ->prefix('৳')
                                    ->default(0)
                                    ->minValue(0)
                                    ->placeholder('Cost incurred'),
                            ]),
                        Section::make('Additional Information')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Textarea::make('notes')
                                    ->placeholder('Any further details about this wastage instance...')
                                    ->rows(4)
                                    ->columnSpanFull(),
                            ])
                            ->collapsible(),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}
