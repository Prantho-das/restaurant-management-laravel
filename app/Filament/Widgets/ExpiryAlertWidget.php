<?php

namespace App\Filament\Widgets;

use App\Models\PurchaseItem;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class ExpiryAlertWidget extends TableWidget
{
    protected static ?int $sort = 1;

    protected static ?string $heading = 'Ingredient Expiry Alerts';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => PurchaseItem::query()
                ->whereNotNull('expiry_date')
                ->where('expiry_date', '<=', now()->addDays(7))
                ->orderBy('expiry_date', 'asc')
            )
            ->columns([
                TextColumn::make('ingredient.name')
                    ->label('Ingredient')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('expiry_date')
                    ->date()
                    ->sortable()
                    ->color(fn (PurchaseItem $record): string => 
                        $record->expiry_date->isPast() ? 'danger' : 'warning'
                    ),
            ]);
    }
}
