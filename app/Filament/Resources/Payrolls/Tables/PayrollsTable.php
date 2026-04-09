<?php

namespace App\Filament\Resources\Payrolls\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Table;
use App\Filament\Resources\Payrolls\Exports\PayrollExporter;

class PayrollsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Employee')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('month')
                    ->searchable(),
                TextColumn::make('year')
                    ->sortable(),
                TextColumn::make('base_salary')
                    ->money('BDT')
                    ->sortable(),
                TextColumn::make('bonus_amount')
                    ->money('BDT')
                    ->sortable(),
                TextColumn::make('deduction_amount')
                    ->money('BDT')
                    ->sortable(),
                TextColumn::make('advance_amount')
                    ->money('BDT')
                    ->sortable(),
                TextColumn::make('net_paid')
                    ->money('BDT')
                    ->sortable()
                    ->summarize(Sum::make()->label('Total')),
                TextColumn::make('payment_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Paid' => 'success',
                        'Pending' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                ExportAction::make()->exporter(PayrollExporter::class),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
