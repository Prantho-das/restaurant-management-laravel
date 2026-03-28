<?php

namespace App\Filament\Resources\AuditLogs\Tables;

use Filament\Actions\ViewAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AuditLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('causer.name')
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->default('System'),
                TextColumn::make('subject_type')
                    ->label('Resource')
                    ->formatStateUsing(fn ($state) => str_replace('App\\Models\\', '', $state))
                    ->badge()
                    ->searchable(),
                TextColumn::make('event')
                    ->label('Action')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default => 'gray',
                    })
                    ->searchable(),
                TextColumn::make('description')
                    ->label('Detail')
                    ->searchable()
                    ->limit(30),
                TextColumn::make('created_at')
                    ->label('Timestamp')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('event')
                    ->options([
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'deleted' => 'Deleted',
                    ]),
                SelectFilter::make('subject_type')
                    ->options([
                        'App\\Models\\Order' => 'Orders',
                        'App\\Models\\MenuItem' => 'Menu Items',
                    ]),
            ])
            ->actions([
                ViewAction::make()
                    // ->modalTitle('Activity Details')
                    ->form([
                        TextInput::make('created_at')
                            ->label('Occurred At'),
                        KeyValue::make('properties.old')
                            ->label('Previous Values')
                            ->visible(fn ($record) => isset($record->properties['old'])),
                        KeyValue::make('properties.attributes')
                            ->label('New Values')
                            ->visible(fn ($record) => isset($record->properties['attributes'])),
                    ]),
            ]);
    }
}
