<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\Pages\Pages\ManagePages;
use App\Models\Page;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-document-duplicate';

    protected static \UnitEnum|string|null $navigationGroup = 'Setup';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Page Information')
                    ->description('Basic page details')
                    ->headerActions([
                        Action::make('preview')
                            ->label('Preview')
                            ->icon('heroicon-o-eye')
                            ->url(fn () => route('page.show', ['slug' => 'sample']))
                            ->openUrlInNewTab(),
                    ])
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('title')
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(fn (string $operation, $state, $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                                TextInput::make('slug')
                                    ->required()
                                    ->unique(ignoreRecord: true),
                            ]),
                        Grid::make(2)
                            ->schema([
                                Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(true),
                                Toggle::make('show_in_footer')
                                    ->label('Show in Footer')
                                    ->default(true),
                            ]),
                    ])->columnSpanFull(),

                Section::make('Page Content (Blocks)')
                    ->description('Build your page using modular sections. Each section can have a different design.')
                    ->headerActions([
                        // Action::make('addTemplate')
                        //     ->label('Load Template')
                        //     ->icon('heroicon-o-document-plus')
                        //     ->action(function (array $data) {
                        //         // Add your template loading logic here
                        //     }),
                    ])
                    ->schema([
                        Builder::make('content')
                            ->blocks([
                                Block::make('hero')
                                    ->icon('heroicon-o-sparkles')
                                    ->schema([
                                        TextInput::make('title')->required(),
                                        TextInput::make('subtitle'),
                                        RichEditor::make('description'),
                                        FileUpload::make('image')->image()->disk('public')->directory('pages'),
                                        Select::make('alignment')
                                            ->options([
                                                'left' => 'Left',
                                                'center' => 'Center',
                                                'right' => 'Right',
                                            ])->default('center'),
                                        // Optional Button
                                        Grid::make(3)
                                            ->schema([
                                                TextInput::make('button_text')->label('Button Text'),
                                                TextInput::make('button_url')->label('Button URL'),
                                                Select::make('button_style')
                                                    ->label('Button Style')
                                                    ->options([
                                                        'primary' => 'Primary',
                                                        'secondary' => 'Secondary',
                                                        'outline' => 'Outline',
                                                    ])->default('primary'),
                                            ]),
                                    ]),
                                Block::make('rich_text')
                                    ->icon('heroicon-o-document-text')
                                    ->schema([
                                        RichEditor::make('content')->required(),
                                        Select::make('width')
                                            ->options([
                                                'standard' => 'Standard',
                                                'wide' => 'Wide',
                                                'narrow' => 'Narrow (Centered)',
                                            ])->default('standard'),
                                        // Optional Button
                                        Grid::make(3)
                                            ->schema([
                                                TextInput::make('button_text')->label('Button Text'),
                                                TextInput::make('button_url')->label('Button URL'),
                                                Select::make('button_style')
                                                    ->label('Button Style')
                                                    ->options([
                                                        'primary' => 'Primary',
                                                        'secondary' => 'Secondary',
                                                        'outline' => 'Outline',
                                                    ])->default('primary'),
                                            ]),
                                    ]),
                                Block::make('image_text')
                                    ->label('Image + Text')
                                    ->icon('heroicon-o-photo')
                                    ->schema([
                                        TextInput::make('title'),
                                        RichEditor::make('content'),
                                        FileUpload::make('image')->image()->directory('pages'),
                                        Select::make('image_position')
                                            ->label('Image Position')
                                            ->options([
                                                'left' => 'Left',
                                                'right' => 'Right',
                                            ])->default('left'),
                                        // Optional Button
                                        Grid::make(3)
                                            ->schema([
                                                TextInput::make('button_text')->label('Button Text'),
                                                TextInput::make('button_url')->label('Button URL'),
                                                Select::make('button_style')
                                                    ->label('Button Style')
                                                    ->options([
                                                        'primary' => 'Primary',
                                                        'secondary' => 'Secondary',
                                                        'outline' => 'Outline',
                                                    ])->default('primary'),
                                            ]),
                                    ]),
                                Block::make('featured_menu')
                                    ->icon('heroicon-o-list-bullet')
                                    ->schema([
                                        TextInput::make('title')->default('Featured Delicacies'),
                                        TextInput::make('subtitle'),
                                        // Optional Button
                                        Grid::make(3)
                                            ->schema([
                                                TextInput::make('button_text')->label('Button Text'),
                                                TextInput::make('button_url')->label('Button URL'),
                                                Select::make('button_style')
                                                    ->label('Button Style')
                                                    ->options([
                                                        'primary' => 'Primary',
                                                        'secondary' => 'Secondary',
                                                        'outline' => 'Outline',
                                                    ])->default('primary'),
                                            ]),
                                    ]),
                                Block::make('faq')
                                    ->label('FAQ Section')
                                    ->icon('heroicon-o-question-mark-circle')
                                    ->schema([
                                        TextInput::make('title')->default('Frequently Asked Questions'),
                                        Repeater::make('items')
                                            ->schema([
                                                TextInput::make('question')->required(),
                                                Textarea::make('answer')->required(),
                                            ])->collapsible()->itemLabel(fn ($state) => $state['question'] ?? 'New Item'),
                                        // Optional Button
                                        Grid::make(3)
                                            ->schema([
                                                TextInput::make('button_text')->label('Button Text'),
                                                TextInput::make('button_url')->label('Button URL'),
                                                Select::make('button_style')
                                                    ->label('Button Style')
                                                    ->options([
                                                        'primary' => 'Primary',
                                                        'secondary' => 'Secondary',
                                                        'outline' => 'Outline',
                                                    ])->default('primary'),
                                            ]),
                                    ]),
                            ])
                            ->collapsible()
                            ->collapsed(),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('slug')
                    ->searchable(),
                IconColumn::make('is_active')
                    ->boolean(),
                IconColumn::make('show_in_footer')
                    ->boolean(),
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
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePages::route('/'),
        ];
    }
}
