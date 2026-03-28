<?php

namespace App\Filament\Resources\Wastages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class WastagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('date', 'desc')
            ->columns([
                TextColumn::make('date')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('ingredient.name')
                    ->label('Ingredient')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('menuItem.name')
                    ->label('Menu Item')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('quantity')
                    ->numeric(decimalPlaces: 3)
                    ->sortable(),
                TextColumn::make('unit')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('reason')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'expired' => 'danger',
                        'damaged' => 'warning',
                        'spillage' => 'info',
                        'preparation_error' => 'primary',
                        'quality_issue' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'expired' => 'Expired',
                        'damaged' => 'Damaged',
                        'spillage' => 'Spillage',
                        'preparation_error' => 'Prep Error',
                        'quality_issue' => 'Quality Issue',
                        default => ucfirst($state),
                    })
                    ->sortable(),
                TextColumn::make('estimated_cost')
                    ->label('Est. Cost')
                    ->numeric(decimalPlaces: 2)
                    ->prefix('৳')
                    ->sortable()
                    ->weight('bold')
                    ->color('danger'),
                TextColumn::make('user.name')
                    ->label('Recorded By')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('reason')
                    ->options([
                        'expired' => 'Expired',
                        'damaged' => 'Damaged',
                        'spillage' => 'Spillage',
                        'preparation_error' => 'Preparation Error',
                        'quality_issue' => 'Quality Issue',
                        'other' => 'Other',
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
