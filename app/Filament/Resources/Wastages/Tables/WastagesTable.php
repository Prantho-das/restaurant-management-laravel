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
                    ->sortable()
                    ->icon('heroicon-o-calendar')
                    ->iconColor('primary'),
                TextColumn::make('ingredient.name')
                    ->label('Wasted Item')
                    ->searchable()
                    ->getStateUsing(function ($record) {
                        return $record->ingredient?->name ?? $record->menuItem?->name ?? '—';
                    })
                    ->description(function ($record) {
                        return $record->ingredient_id ? 'Ingredient' : ($record->menu_item_id ? 'Menu Item' : 'Unknown');
                    }),
                TextColumn::make('quantity')
                    ->numeric(decimalPlaces: 3)
                    ->sortable()
                    ->formatStateUsing(fn ($state, $record) => $state.' '.$record->unit)
                    ->color('warning')
                    ->badge(),
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
                    ->icon(fn (string $state): string => match ($state) {
                        'expired' => 'heroicon-o-clock',
                        'damaged' => 'heroicon-o-x-circle',
                        'spillage' => 'heroicon-o-beaker',
                        'preparation_error' => 'heroicon-o-wrench',
                        'quality_issue' => 'heroicon-o-hand-thumb-down',
                        default => 'heroicon-o-question-mark-circle',
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
                TextColumn::make('notes')
                    ->label('Notes')
                    ->limit(30)
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
                    ])
                    ->native(false),
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
