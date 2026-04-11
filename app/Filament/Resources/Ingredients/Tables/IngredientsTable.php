<?php

namespace App\Filament\Resources\Ingredients\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class IngredientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('category')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Food' => 'info',
                        'Supply' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('unit')
                    ->searchable(),
                TextColumn::make('current_stock')
                    ->numeric()
                    ->sortable()
                    ->color(fn ($record) => $record->current_stock <= $record->alert_threshold ? 'danger' : 'success')
                    ->badge(),
                TextColumn::make('alert_threshold')
                    ->numeric()
                    ->sortable()
                    ->color('gray'),
                TextColumn::make('unit_cost')
                    ->numeric(2)
                    ->prefix('৳')
                    ->sortable()
                    ->color('info'),
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
                SelectFilter::make('category')
                    ->options([
                        'Food' => 'Food',
                        'Supply' => 'Supply',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
