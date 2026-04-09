<?php

namespace App\Filament\Resources\Expenses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use App\Filament\Resources\Expenses\Exports\ExpenseExporter;

class ExpensesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('date', 'desc')
            ->columns([
                TextColumn::make('date')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('category')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'rent' => 'info',
                        'utilities' => 'warning',
                        'salary' => 'success',
                        'supplies' => 'primary',
                        'maintenance' => 'gray',
                        'marketing' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable(),
                TextColumn::make('title')
                    ->searchable()
                    ->weight('bold')
                    ->limit(40),
                TextColumn::make('amount')
                    ->numeric(decimalPlaces: 2)
                    ->prefix('৳')
                    ->sortable()
                    ->weight('bold')
                    ->color('danger')
                    ->summarize(Sum::make()->label('Total')),
                TextColumn::make('payment_method')
                    ->label('Payment')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'cash' => 'success',
                        'mobile_pay' => 'info',
                        'card' => 'warning',
                        'bank_transfer' => 'primary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'cash' => 'Cash',
                        'mobile_pay' => 'bKash',
                        'card' => 'Card',
                        'bank_transfer' => 'Bank Transfer',
                        default => ucfirst($state),
                    }),
                TextColumn::make('reference_no')
                    ->label('Ref #')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('user.name')
                    ->label('Recorded By')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->options([
                        'rent' => 'Rent',
                        'utilities' => 'Utilities',
                        'salary' => 'Salary',
                        'supplies' => 'Supplies',
                        'maintenance' => 'Maintenance',
                        'marketing' => 'Marketing',
                        'other' => 'Other',
                    ]),
                SelectFilter::make('payment_method')
                    ->label('Payment Method')
                    ->options([
                        'cash' => 'Cash',
                        'mobile_pay' => 'bKash',
                        'card' => 'Card',
                        'bank_transfer' => 'Bank Transfer',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                ExportAction::make()->exporter(ExpenseExporter::class),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
