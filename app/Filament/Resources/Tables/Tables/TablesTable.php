<?php

namespace App\Filament\Resources\Tables\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TablesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'available' => 'success',
                        'occupied' => 'danger',
                        'reserved' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('outlet.name')
                    ->badge()
                    ->color('info')
                    ->searchable(),
                TextColumn::make('capacity')
                    ->numeric()
                    ->alignCenter()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'available' => 'Available',
                        'occupied' => 'Occupied',
                        'reserved' => 'Reserved',
                    ]),
                SelectFilter::make('outlet_id')
                    ->relationship('outlet', 'name'),
            ])
            ->actions([
                EditAction::make(),
                Action::make('view_qr')
                    ->label('QR Code')
                    ->icon('heroicon-o-qr-code')
                    ->color('info')
                    ->modalContent(fn ($record) => new HtmlString('
                        <div class="flex flex-col items-center justify-center p-4 gap-4">
                            '.QrCode::size(250)->generate($record->qr_code_url).'
                            <div class="text-center">
                                <p class="text-lg font-bold">'.$record->name.'</p>
                                <p class="text-sm text-gray-500">'.$record->qr_code_url.'</p>
                            </div>
                            <a href="data:image/svg+xml;base64,'.base64_encode(QrCode::size(500)->generate($record->qr_code_url)).'" 
                               download="qr-'.$record->slug.'.svg"
                               class="px-4 py-2 bg-primary-600 text-white rounded-lg shadow hover:bg-primary-500 transition-colors">
                               Download SVG
                            </a>
                        </div>
                    '))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
