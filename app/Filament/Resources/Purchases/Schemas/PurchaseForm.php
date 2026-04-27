<?php

namespace App\Filament\Resources\Purchases\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PurchaseForm
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
                            ->default('PO-'.strtoupper(Str::random(10)))
                            ->unique(ignoreRecord: true),
                        Select::make('supplier_id')
                            ->relationship('supplier', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        DatePicker::make('purchase_date')
                            ->required()
                            ->default(now()),
                        Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'ordered' => 'Ordered',
                                'received' => 'Received',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required()
                            ->default('pending')
                            ->selectablePlaceholder(false),
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->default(auth()->id())
                            ->required()
                            ->searchable()
                            ->columnSpanFull(),
                    ]),
                Section::make('Total & Notes')
                    ->columns(3)
                    ->schema([
                        TextInput::make('discount')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->prefix('BDT')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Get $get, Set $set) => self::updateTotalAmount($get, $set)),
                        TextInput::make('total_amount')
                            ->prefix('BDT')
                            ->readOnly(),
                        Textarea::make('notes')
                            ->rows(3)
                            ->columnSpan(1),
                    ]),
                Section::make('Purchase Items')
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
                                TextInput::make('quantity')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0.01)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Get $get, Set $set) => self::updateSubtotal($get, $set)),
                                TextInput::make('unit_price')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->prefix('BDT')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Get $get, Set $set) => self::updateSubtotal($get, $set)),
                                TextInput::make('subtotal')
                                    ->required()
                                    ->numeric()
                                    ->readOnly()
                                    ->prefix('BDT'),
                                DatePicker::make('expiry_date')
                                    ->label('Expiry Date')
                                    ->nullable(),
                            ])
                            ->columns(5)
                            ->live()
                            ->afterStateUpdated(fn (Get $get, Set $set) => self::updateTotalAmount($get, $set)),
                    ])->columnSpanFull(),

            ]);
    }

    public static function updateTotalAmount(Get $get, Set $set): void
    {
        $items = $get('items') ?? [];
        $subtotalSum = collect($items)->sum(fn (?array $item) => floatval($item['subtotal'] ?? 0));
        $discount = $get->float('discount');

        $total = max(0, $subtotalSum - $discount);

        $set('total_amount', number_format($total, 2, '.', ''));
    }

    protected static function updateSubtotal(Get $get, Set $set): void
    {
        $quantity = $get->float('quantity');
        $unitPrice = $get->float('unit_price');
        $set('subtotal', number_format($quantity * $unitPrice, 2, '.', ''));

        $items = $get('../../items') ?? [];
        $subtotalSum = collect($items)->sum(fn (?array $item) => floatval($item['subtotal'] ?? 0));
        $discount = $get->float('../../discount');

        $total = max(0, $subtotalSum - $discount);
        $set('../../total_amount', number_format($total, 2, '.', ''));
    }
}
