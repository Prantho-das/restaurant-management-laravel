<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use UnitEnum;

class ManageLandingPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-window';

    protected string $view = 'filament.pages.manage-landing-page';

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 3;

    protected static ?string $title = 'Landing Page';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = Setting::where('group', 'landing_page')->get();
        $this->form->fill($settings->pluck('value', 'key')->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Hero Section')
                    ->schema([
                        TextInput::make('lp_hero_title'),
                        TextInput::make('lp_hero_subtitle'),
                        Textarea::make('lp_hero_description')
                            ->columnSpanFull(),
                        FileUpload::make('lp_hero_image')
                            ->image()
                            ->disk('public')
                            ->directory('landing-page'),
                    ])->columns(2),

                Section::make('Heritage Section')
                    ->schema([
                        TextInput::make('lp_heritage_title'),
                        TextInput::make('lp_heritage_subtitle'),
                        Textarea::make('lp_heritage_description')
                            ->columnSpanFull(),
                        FileUpload::make('lp_heritage_image_1')
                            ->image()
                            ->disk('public')
                            ->directory('landing-page'),
                        FileUpload::make('lp_heritage_image_2')
                            ->image()
                            ->disk('public')
                            ->directory('landing-page'),
                        FileUpload::make('lp_heritage_image_3')
                            ->image()
                            ->disk('public')
                            ->directory('landing-page'),
                    ])->columns(2),

                Section::make('Secret Section')
                    ->schema([
                        TextInput::make('lp_secret_title'),
                        TextInput::make('lp_secret_subtitle'),
                        Textarea::make('lp_secret_description')
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Mission & Vision')
                    ->description('Communicate your brand\'s purpose and future goals.')
                    ->schema([
                        Section::make('Our Mission')
                            ->schema([
                                TextInput::make('lp_mission_title')
                                    ->label('Mission Title'),
                                TextInput::make('lp_mission_subtitle')
                                    ->label('Mission Subtitle'),
                                Textarea::make('lp_mission_description')
                                    ->label('Mission Description')
                                    ->columnSpanFull(),
                                FileUpload::make('lp_mission_image')
                                    ->disk('public')
                                    ->label('Mission Image')
                                    ->image()
                                    ->directory('landing-page'),
                            ])->columns(2),
                        Section::make('Our Vision')
                            ->schema([
                                TextInput::make('lp_vision_title')
                                    ->label('Vision Title'),
                                TextInput::make('lp_vision_subtitle')
                                    ->label('Vision Subtitle'),
                                Textarea::make('lp_vision_description')
                                    ->label('Vision Description')
                                    ->columnSpanFull(),
                                FileUpload::make('lp_vision_image')
                                    ->disk('public')
                                    ->label('Vision Image')
                                    ->image()
                                    ->directory('landing-page'),
                            ])->columns(2),
                    ]),

                Section::make('Visual Story Section')
                    ->schema([
                        TextInput::make('lp_visual_story_title')
                            ->columnSpanFull(),
                        TextInput::make('lp_visual_story_subtitle')
                            ->columnSpanFull(),
                        FileUpload::make('lp_visual_story_image_1')
                            ->image()
                            ->disk('public')
                            ->directory('landing-page')
                            ->label('Gallery Image 1 (Large)'),
                        FileUpload::make('lp_visual_story_image_2')
                            ->image()
                            ->disk('public')
                            ->directory('landing-page')
                            ->label('Gallery Image 2 (Square)'),
                        FileUpload::make('lp_visual_story_image_3')
                            ->image()
                            ->disk('public')
                            ->directory('landing-page')
                            ->label('Gallery Image 3 (Square)'),
                        FileUpload::make('lp_visual_story_image_4')
                            ->image()
                            ->disk('public')
                            ->directory('landing-page')
                            ->label('Gallery Image 4 (Wide)'),
                    ])->columns(2),
                // Section::make('Status')
                //     ->schema([
                //         Toggle::make('lp_is_active')
                //             ->label('Is Landing Page Active?')
                //             ->default(true),
                //     ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            Setting::setValue($key, $value, 'landing_page');
        }

        Notification::make()
            ->title('Settings saved successfully')
            ->success()
            ->send();
    }
}
