<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('order_number')
                    ->label('Order #')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable(),
                TextColumn::make('order_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'dine_in' => 'success',
                        'takeaway' => 'warning',
                        'delivery' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'dine_in' => 'Dine In',
                        'takeaway' => 'Takeaway',
                        'delivery' => 'Delivery',
                        default => ucfirst($state),
                    })
                    ->sortable(),
                TextColumn::make('customer_name')
                    ->label('Customer')
                    ->searchable()
                    ->default('Walk-in')
                    ->color('gray'),
                TextColumn::make('customer_phone')
                    ->label('Phone')
                    ->searchable()
                    ->placeholder('N/A')
                    ->toggleable(),
                TextColumn::make('payment_method')
                    ->label('Payment')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'cash' => 'success',
                        'mobile_pay' => 'info',
                        'card' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'cash' => 'Cash',
                        'mobile_pay' => 'bKash',
                        'card' => 'Card',
                        default => ucfirst($state),
                    }),
                TextColumn::make('items_count')
                    ->label('Items')
                    ->counts('items')
                    ->sortable(),
                TextColumn::make('subtotal_amount')
                    ->label('Subtotal')
                    ->numeric(decimalPlaces: 2)
                    ->prefix('৳')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('discount_amount')
                    ->label('Discount')
                    ->numeric(decimalPlaces: 2)
                    ->prefix('-৳')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->color('danger'),
                TextColumn::make('total_amount')
                    ->label('Total')
                    ->numeric(decimalPlaces: 2)
                    ->prefix('৳')
                    ->sortable()
                    ->weight('bold')
                    ->color('success'),
                TextColumn::make('reference_no')
                    ->label('Ref #')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('table_number')
                    ->label('Table')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d M Y, h:i A')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
                SelectFilter::make('order_type')
                    ->label('Order Type')
                    ->options([
                        'dine_in' => 'Dine In',
                        'takeaway' => 'Takeaway',
                        'delivery' => 'Delivery',
                    ]),
                SelectFilter::make('payment_method')
                    ->label('Payment Method')
                    ->options([
                        'cash' => 'Cash',
                        'mobile_pay' => 'bKash',
                        'card' => 'Card',
                    ]),
            ])
            ->recordActions([
                Action::make('printReceipt')
                    ->label('Receipt')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->action(fn ($record, $livewire) => $livewire->dispatch('print-order-receipt', receipt: $record->toReceiptArray())),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
