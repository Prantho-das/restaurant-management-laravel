<?php

namespace App\Filament\Resources\Inventory\StockAdjustments\Tables;

use App\Models\StockAdjustment;
use App\Services\InventoryService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StockAdjustmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference_no')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                TextColumn::make('adjustment_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'completed' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                TextColumn::make('user.name')
                    ->label('Admin')
                    ->sortable(),
                TextColumn::make('items_count')
                    ->counts('items')
                    ->label('Items'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('complete')
                    ->label('Complete')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (StockAdjustment $record): bool => $record->status === 'draft')
                    ->action(function (StockAdjustment $record, InventoryService $inventoryService) {
                        if ($record->items()->count() === 0) {
                            Notification::make()
                                ->title('No items added')
                                ->danger()
                                ->send();

                            return;
                        }

                        $inventoryService->processStockAdjustment($record);

                        $record->update(['status' => 'completed']);

                        Notification::make()
                            ->title('Stock adjustment completed successfully')
                            ->success()
                            ->send();
                    }),
                ViewAction::make(),
                EditAction::make()
                    ->disabled(fn (StockAdjustment $record): bool => $record->status === 'completed'),
                DeleteAction::make()
                    ->disabled(fn (StockAdjustment $record): bool => $record->status === 'completed'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                if ($record->status !== 'completed') {
                                    $record->delete();
                                }
                            }
                        }),
                ]),
            ]);
    }
}
