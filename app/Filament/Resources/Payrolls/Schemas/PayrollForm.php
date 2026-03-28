<?php

namespace App\Filament\Resources\Payrolls\Schemas;

use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PayrollForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('Employee')
                    ->options(User::all()->pluck('name', 'id'))
                    ->required()
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function ($state, $set) {
                        $user = User::find($state);
                        if ($user) {
                            $set('base_salary', $user->base_salary);
                            $set('net_paid', $user->base_salary);
                        }
                    }),
                DatePicker::make('payment_date')
                    ->default(now())
                    ->required(),
                Select::make('month')
                    ->options([
                        'January' => 'January', 'February' => 'February', 'March' => 'March',
                        'April' => 'April', 'May' => 'May', 'June' => 'June',
                        'July' => 'July', 'August' => 'August', 'September' => 'September',
                        'October' => 'October', 'November' => 'November', 'December' => 'December',
                    ])
                    ->default(now()->format('F'))
                    ->required(),
                TextInput::make('year')
                    ->default(now()->year)
                    ->required()
                    ->numeric(),
                TextInput::make('base_salary')
                    ->required()
                    ->numeric()
                    ->prefix('$')
                    ->live()
                    ->afterStateUpdated(fn ($set, $get) => self::calculateNet($set, $get)),
                TextInput::make('bonus_amount')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('$')
                    ->live()
                    ->afterStateUpdated(fn ($set, $get) => self::calculateNet($set, $get)),
                TextInput::make('deduction_amount')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('$')
                    ->live()
                    ->afterStateUpdated(fn ($set, $get) => self::calculateNet($set, $get)),
                TextInput::make('net_paid')
                    ->required()
                    ->numeric()
                    ->readonly()
                    ->prefix('$'),
                Select::make('payment_method')
                    ->options([
                        'Cash' => 'Cash',
                        'Bank Transfer' => 'Bank Transfer',
                        'Mobile Banking' => 'Mobile Banking',
                    ])
                    ->required(),
                Select::make('status')
                    ->options([
                        'Pending' => 'Pending',
                        'Paid' => 'Paid',
                    ])
                    ->required()
                    ->default('Pending'),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }

    public static function calculateNet($set, $get): void
    {
        $base = (float) $get('base_salary');
        $bonus = (float) $get('bonus_amount');
        $deduction = (float) $get('deduction_amount');

        $set('net_paid', number_format($base + $bonus - $deduction, 2, '.', ''));
    }
}
