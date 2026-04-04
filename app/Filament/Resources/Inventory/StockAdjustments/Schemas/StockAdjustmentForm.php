<?php

namespace App\Filament\Resources\Inventory\StockAdjustments\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class StockAdjustmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('General Information')
                    ->columns(2)
                    ->schema([
                        TextInput::make('reference_no')
                            ->required()
                            ->default('SA-'.strtoupper(Str::random(10)))
                            ->unique(ignoreRecord: true),
                        DatePicker::make('adjustment_date')
                            ->required()
                            ->default(now()),
                        Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'completed' => 'Completed',
                            ])
                            ->required()
                            ->default('draft')
                            ->selectablePlaceholder(false)
                            ->disabled(fn ($record) => $record && $record->status === 'completed'),
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->default(auth()->id())
                            ->required()
                            ->searchable()
                            ->preload()
                            ->columnSpanFull()
                            ->disabled(fn ($record) => $record && $record->status === 'completed'),
                    ]),

                Section::make('Adjustment Items')
                    ->schema([
                        Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Select::make('ingredient_id')
                                    ->relationship('ingredient', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->distinct()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                                Select::make('type')
                                    ->options([
                                        'addition' => 'Addition (+)',
                                        'subtraction' => 'Subtraction (-)',
                                    ])
                                    ->required()
                                    ->default('addition'),
                                TextInput::make('quantity')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0.001),
                                TextInput::make('note')
                                    ->placeholder('Reason for item adjustment'),
                            ])
                            ->columns(4)
                            ->disabled(fn ($record) => $record && $record->status === 'completed')
                            ->addable(fn ($record) => ! ($record && $record->status === 'completed'))
                            ->deletable(fn ($record) => ! ($record && $record->status === 'completed'))
                            ->reorderable(fn ($record) => ! ($record && $record->status === 'completed')),
                    ]),

                Section::make('Notes')
                    ->schema([
                        Textarea::make('notes')
                            ->rows(3)
                            ->disabled(fn ($record) => $record && $record->status === 'completed'),
                    ]),
            ]);
    }
}
