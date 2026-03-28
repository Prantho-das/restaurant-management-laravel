<?php

namespace App\Filament\Resources\Expenses\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ExpenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Expense Details')
                    ->schema([
                        Select::make('category')
                            ->options([
                                'rent' => 'Rent',
                                'utilities' => 'Utilities',
                                'salary' => 'Salary',
                                'supplies' => 'Supplies',
                                'maintenance' => 'Maintenance',
                                'marketing' => 'Marketing',
                                'other' => 'Other',
                            ])
                            ->required()
                            ->searchable(),
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('amount')
                            ->required()
                            ->numeric()
                            ->prefix('৳')
                            ->minValue(0),
                        DatePicker::make('date')
                            ->required()
                            ->default(now()),
                    ])
                    ->columns(2),
                Section::make('Payment Information')
                    ->schema([
                        Select::make('payment_method')
                            ->options([
                                'cash' => 'Cash',
                                'mobile_pay' => 'bKash',
                                'card' => 'Card',
                                'bank_transfer' => 'Bank Transfer',
                            ])
                            ->required()
                            ->default('cash'),
                        TextInput::make('reference_no')
                            ->label('Reference No / Transaction ID'),
                        FileUpload::make('receipt')
                            ->label('Receipt / Document')
                            ->directory('expenses/receipts')
                            ->image()
                            ->maxSize(2048)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Additional Info')
                    ->schema([
                        Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }
}
