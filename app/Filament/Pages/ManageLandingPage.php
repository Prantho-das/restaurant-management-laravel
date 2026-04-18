<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
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

    protected static string|UnitEnum|null $navigationGroup = 'Setup';

    protected static ?int $navigationSort = 3;

    protected static ?string $title = 'Landing Page';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = Setting::where('group', 'landing_page')->get();

        $formData = $settings->pluck('value', 'key')->toArray();

        if (! empty($formData['lp_custom_blocks'])) {
            $decodedCustomBlocks = json_decode((string) $formData['lp_custom_blocks'], true);
            $formData['lp_custom_blocks'] = is_array($decodedCustomBlocks) ? $decodedCustomBlocks : [];
        }

        $this->form->fill($formData);
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
                            ->disk('public')->maxSize(5120)
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
                            ->disk('public')->maxSize(5120)
                            ->directory('landing-page'),
                        FileUpload::make('lp_heritage_image_2')
                            ->image()
                            ->disk('public')->maxSize(5120)
                            ->directory('landing-page'),
                        FileUpload::make('lp_heritage_image_3')
                            ->image()
                            ->disk('public')->maxSize(5120)
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
                                    ->disk('public')->maxSize(5120)
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
                                    ->disk('public')->maxSize(5120)
                                    ->label('Vision Image')
                                    ->image()
                                    ->directory('landing-page'),
                            ])->columns(2),
                    ]),

                Section::make('Stats Section')
                    ->description('Display key metrics about your restaurant.')
                    ->schema([
                        TextInput::make('lp_stats_experience')
                            ->label('Experience Value')
                            ->placeholder('e.g., 50+'),
                        TextInput::make('lp_stats_experience_label')
                            ->label('Experience Label')
                            ->placeholder('e.g., বছর অভিজ্ঞতা'),
                        TextInput::make('lp_stats_foods')
                            ->label('Foods Value')
                            ->placeholder('e.g., 100+'),
                        TextInput::make('lp_stats_foods_label')
                            ->label('Foods Label')
                            ->placeholder('e.g., খাবার'),
                        TextInput::make('lp_stats_customers')
                            ->label('Customers Value')
                            ->placeholder('e.g., 5000+'),
                        TextInput::make('lp_stats_customers_label')
                            ->label('Customers Label')
                            ->placeholder('e.g., গ্রাহক'),
                    ])->columns(2),

                Section::make('Signature Menu Section')
                    ->schema([
                        TextInput::make('lp_menu_title')
                            ->label('Section Title')
                            ->placeholder('আমাদের স্পেশাল'),
                        TextInput::make('lp_menu_subtitle')
                            ->label('Section Subtitle')
                            ->placeholder('জনপ্রিয় মেনু'),
                        Textarea::make('lp_menu_description')
                            ->label('Description')
                            ->columnSpanFull(),
                        TextInput::make('lp_menu_button_text')
                            ->label('Button Text')
                            ->placeholder('সব মেনু দেখুন'),
                    ])->columns(2),

                Section::make('Visual Story Section')
                    ->schema([
                        TextInput::make('lp_visual_story_title')
                            ->columnSpanFull(),
                        TextInput::make('lp_visual_story_subtitle')
                            ->columnSpanFull(),
                        FileUpload::make('lp_visual_story_image_1')
                            ->image()
                            ->disk('public')->maxSize(5120)
                            ->directory('landing-page')
                            ->label('Gallery Image 1 (Large)'),
                        FileUpload::make('lp_visual_story_image_2')
                            ->image()
                            ->disk('public')->maxSize(5120)
                            ->directory('landing-page')
                            ->label('Gallery Image 2 (Square)'),
                        FileUpload::make('lp_visual_story_image_3')
                            ->image()
                            ->disk('public')->maxSize(5120)
                            ->directory('landing-page')
                            ->label('Gallery Image 3 (Square)'),
                        FileUpload::make('lp_visual_story_image_4')
                            ->image()
                            ->disk('public')->maxSize(5120)
                            ->directory('landing-page')
                            ->label('Gallery Image 4 (Wide)'),
                    ])->columns(2),

                Section::make('Reservation Section')
                    ->description('Manage reservation call-to-action content shown on homepage.')
                    ->schema([
                        TextInput::make('lp_reservation_subtitle')
                            ->label('Reservation Subtitle')
                            ->placeholder('টেবিল বুকিং'),
                        TextInput::make('lp_reservation_title')
                            ->label('Reservation Title')
                            ->placeholder('আপনার জন্য টেবিল রিজার্ভ'),
                        Textarea::make('lp_reservation_description')
                            ->label('Reservation Description')
                            ->placeholder('প্রাইভেট ইভেন্ট ও কর্পোরেট গাদারিং এর জন্য...')
                            ->columnSpanFull(),
                        TextInput::make('lp_reservation_contact_label')
                            ->label('Reservation Contact Label')
                            ->placeholder('হেল্পলাইন'),
                    ])->columns(2),

                Section::make('Delivery Partner Section')
                    ->description('Manage online order partner section content shown on homepage.')
                    ->schema([
                        TextInput::make('lp_delivery_title')
                            ->label('Delivery Title')
                            ->placeholder('অনলাইন অর্ডার প্লেস'),
                        Textarea::make('lp_delivery_description')
                            ->label('Delivery Description')
                            ->placeholder('একটি ক্লিকে প্রিয় প্ল্যাটফর্ম...')
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Reviews Section')
                    ->description('Control review section heading content on homepage.')
                    ->schema([
                        TextInput::make('lp_reviews_subtitle')
                            ->label('Reviews Subtitle')
                            ->placeholder('আমাদের গ্রাহকদের কথা'),
                        TextInput::make('lp_reviews_title')
                            ->label('Reviews Title')
                            ->placeholder('স্বাদের স্মৃতি'),
                        Textarea::make('lp_reviews_description')
                            ->label('Reviews Description')
                            ->placeholder('আমাদের খাবারের স্বাদ নিয়ে যারা মুগ্ধ...')
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Custom Blocks Section')
                    ->description('Create and manage unlimited custom content blocks for the homepage.')
                    ->schema([
                        Repeater::make('lp_custom_blocks')
                            ->label('Custom Blocks')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Title')
                                    ->required(),
                                TextInput::make('subtitle')
                                    ->label('Subtitle'),
                                Textarea::make('description')
                                    ->label('Description')
                                    ->rows(4)
                                    ->columnSpanFull(),
                                FileUpload::make('image')
                                    ->label('Image')
                                    ->image()
                                    ->disk('public')->maxSize(5120)
                                    ->directory('landing-page/custom-blocks'),
                                TextInput::make('button_text')
                                    ->label('Button Text'),
                                TextInput::make('button_link')
                                    ->label('Button Link')
                                    ->placeholder('https://example.com')
                                    ->url(),
                            ])
                            ->columns(2)
                            ->default([])
                            ->addActionLabel('Add New Block')
                            ->reorderable()
                            ->collapsible()
                            ->columnSpanFull(),
                    ]),
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
            if (is_array($value)) {
                $value = json_encode($value, JSON_UNESCAPED_UNICODE);
            }

            Setting::setValue($key, $value, 'landing_page');
        }

        Notification::make()
            ->title('Settings saved successfully')
            ->success()
            ->send();
    }
}
