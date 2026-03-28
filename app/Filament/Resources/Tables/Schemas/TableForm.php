<?php

namespace App\Filament\Resources\Tables\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TableForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (string $operation, $state, $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('capacity')
                    ->required()
                    ->numeric()
                    ->default(2),
                Select::make('status')
                    ->options([
                        'available' => 'Available',
                        'occupied' => 'Occupied',
                        'reserved' => 'Reserved',
                    ])
                    ->required()
                    ->default('available'),
                Select::make('outlet_id')
                    ->relationship('outlet', 'name')
                    ->required(),
                Placeholder::make('qr_code')
                    ->label('QR Code')
                    ->content(function ($record) {
                        if (! $record || ! $record->slug) {
                            return 'Save table to generate QR code.';
                        }

                        $url = url('/table/'.$record->slug);
                        $svg = QrCode::size(150)->generate($url);

                        return new HtmlString('
                            <div class="flex flex-col items-center gap-2 p-2 bg-white rounded-lg inline-block border border-gray-200">
                                '.$svg.'
                                <span class="text-xs text-gray-500">'.$record->slug.'</span>
                            </div>
                        ');
                    })
                    ->visible(fn ($record) => $record !== null),
            ]);
    }
}
