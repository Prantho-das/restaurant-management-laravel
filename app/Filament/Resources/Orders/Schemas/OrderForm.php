<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\MenuItem;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Order Details')
                    ->schema([
                        TextInput::make('order_number')
                            ->required()
                            ->default(fn () => 'ORD-'.strtoupper(uniqid()))
                            ->unique(ignoreRecord: true)
                            ->disabled(fn ($context) => $context === 'edit')
                            ->dehydrated(),
                        Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'processing' => 'Processing',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required()
                            ->default('pending'),
                        Select::make('order_type')
                            ->options([
                                'dine_in' => 'Dine In',
                                'takeaway' => 'Takeaway',
                                'delivery' => 'Delivery',
                            ])
                            ->required()
                            ->default('dine_in'),
                        Select::make('payment_method')
                            ->options([
                                'cash' => 'Cash',
                                'card' => 'Card',
                                'bkash' => 'bKash',
                                'sslcommerze' => 'SSLCommerze',
                            ])
                            ->required()
                            ->default('cash'),
                    ])
                    ->columns(2),
                Section::make('Customer Information')
                    ->schema([
                        TextInput::make('customer_name')
                            ->label('Customer Name')
                            ->placeholder('Walk-in Customer'),
                        TextInput::make('table_number')
                            ->label('Table Number'),
                        TextInput::make('guest_count')
                            ->label('Guest Count')
                            ->numeric()
                            ->default(1)
                            ->minValue(1),
                        TextInput::make('reference_no')
                            ->label('Reference No / Transaction ID'),
                    ])
                    ->columns(2),
                Section::make('Order Items')
                    ->schema([
                        Repeater::make('items')
                            ->relationship('items')
                            ->schema([
                                Select::make('menu_item_id')
                                    ->label('Menu Item')
                                    ->relationship('menuItem', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $menuItem = MenuItem::find($state);
                                            if ($menuItem) {
                                                $set('price', $menuItem->final_price);
                                            }
                                        }
                                    }),
                                TextInput::make('quantity')
                                    ->required()
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1),
                                TextInput::make('price')
                                    ->required()
                                    ->numeric()
                                    ->prefix('৳'),
                            ])
                            ->columns(3)
                            ->itemLabel(fn (array $state): ?string => MenuItem::find($state['menu_item_id'] ?? null)?->name ?? null)
                            ->defaultItems(0)
                            ->addActionLabel('Add Item'),
                    ])->columnSpanFull(),
                Section::make('Pricing')
                    ->schema([
                        TextInput::make('subtotal_amount')
                            ->label('Subtotal')
                            ->required()
                            ->numeric()
                            ->prefix('৳')
                            ->default(0),
                        Select::make('discount_type')
                            ->options([
                                'fixed' => 'Fixed (৳)',
                                'percentage' => 'Percentage (%)',
                            ])
                            ->default('fixed'),
                        TextInput::make('discount_amount')
                            ->label('Discount')
                            ->numeric()
                            ->prefix('৳')
                            ->default(0),
                        TextInput::make('total_amount')
                            ->label('Total')
                            ->required()
                            ->numeric()
                            ->prefix('৳')
                            ->default(0),
                    ])
                    ->columns(2),
                Section::make('Notes')
                    ->schema([
                        Textarea::make('notes')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }
}
